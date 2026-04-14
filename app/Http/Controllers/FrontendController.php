<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();

        $activeBorrowings = Borrowing::query()
            ->with('book')
            ->where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->latest('tanggal_pinjam')
            ->get();

        $historyBorrowings = Borrowing::query()
            ->with('book')
            ->where('user_id', $user->id)
            ->where('status', 'dikembalikan')
            ->latest('tanggal_kembali')
            ->limit(5)
            ->get();

        $jumlahTerlambat = Borrowing::query()
            ->where('user_id', $user->id)
            ->where('status', 'terlambat')
            ->count();

        $tenggatTerdekat = Borrowing::query()
            ->with('book')
            ->where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->orderBy('tanggal_jatuh_tempo')
            ->first();

        return view('dashboard', [
            'activeBorrowings' => $activeBorrowings,
            'historyBorrowings' => $historyBorrowings,
            'jumlahTerlambat' => $jumlahTerlambat,
            'tenggatTerdekat' => $tenggatTerdekat,
        ]);
    }

    public function catalog(Request $request): View
    {
        $query = Book::query()->with('category');

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('kode_buku', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $books = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('catalog.index', [
            'books' => $books,
            'categories' => $categories,
        ]);
    }

    public function borrowingHistory(Request $request): View
    {
        $user = Auth::user();

        $history = Borrowing::query()
            ->with(['book', 'returnBook'])
            ->where('user_id', $user->id)
            ->when(
                filled($request->query('status')),
                fn ($query) => $query->where('status', $request->query('status'))
            )
            ->latest('tanggal_pinjam')
            ->paginate(10)
            ->withQueryString();

        return view('borrowings.history', [
            'history' => $history,
        ]);
    }

    public function borrowingNote(Borrowing $borrowing): View
    {
        abort_unless($borrowing->user_id === Auth::id(), 403);

        $borrowing->load(['book', 'returnBook']);

        return view('borrowings.note', [
            'borrowing' => $borrowing,
        ]);
    }

    public function showBook(Book $book): View
    {
        $book->load('category');
        $user = Auth::user();
        $canBorrow = false;
        $borrowRestrictionMessage = 'Login sebagai siswa untuk melakukan peminjaman.';

        if ($user && $user->role === User::ROLE_SISWA) {
            if (! $user->isAccountActive()) {
                $borrowRestrictionMessage = 'Akun Anda belum aktif, silakan menunggu persetujuan admin.';
            } elseif (! $user->student || $user->student->status !== Student::STATUS_AKTIF) {
                $borrowRestrictionMessage = 'Status siswa Anda tidak aktif untuk peminjaman (lulus/keluar).';
            } else {
                $canBorrow = true;
                $borrowRestrictionMessage = '';
            }
        }

        return view('catalog.show', [
            'book' => $book,
            'defaultDuration' => 7,
            'canBorrow' => $canBorrow,
            'borrowRestrictionMessage' => $borrowRestrictionMessage,
        ]);
    }

    public function borrow(Request $request, Book $book): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validate([
            'durasi_hari' => ['required', 'integer', 'min:1', 'max:30'],
        ]);
        $duration = (int) $validated['durasi_hari'];

        if (! $user || $user->role !== User::ROLE_SISWA) {
            return back()->with('error', 'Hanya siswa yang dapat meminjam buku.');
        }

        if (! $user->isAccountActive()) {
            return back()->with('error', 'Akun Anda belum aktif, silakan hubungi admin.');
        }

        if (! $user->student || $user->student->status !== Student::STATUS_AKTIF) {
            return back()->with('error', 'Status siswa Anda tidak aktif untuk peminjaman (lulus/keluar).');
        }

        if ($book->stok <= 0) {
            return back()->with('error', 'Stok buku sedang habis.');
        }

        $hasActiveBorrowing = Borrowing::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'dipinjam')
            ->whereNull('tanggal_kembali')
            ->exists();

        if ($hasActiveBorrowing) {
            return back()->with('error', 'Anda masih memiliki peminjaman aktif untuk buku ini.');
        }

        Borrowing::query()->create([
            'kode_peminjaman' => $this->generateBorrowingCode(),
            'user_id' => $user->id,
            'book_id' => $book->id,
            'admin_id' => null,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays($duration)->toDateString(),
            'status' => 'dipinjam',
            'denda' => 0,
            'catatan' => "Peminjaman dari halaman katalog siswa ({$duration} hari).",
        ]);

        $book->decrement('stok');

        return back()->with('success', "Buku berhasil dipinjam. Batas pengembalian {$duration} hari dari hari ini.");
    }

    private function generateBorrowingCode(): string
    {
        do {
            $code = 'PMJ-' . strtoupper(Str::random(8));
        } while (Borrowing::query()->where('kode_peminjaman', $code)->exists());

        return $code;
    }
}
