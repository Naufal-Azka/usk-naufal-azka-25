<?php

namespace App\Filament\Resources\ReturnBookResource\Pages;

use App\Filament\Resources\ReturnBookResource;
use App\Models\Borrowing;
use App\Models\FineSetting;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateReturnBook extends CreateRecord
{
    protected static string $resource = ReturnBookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $borrowing = Borrowing::query()->with('book')->findOrFail($data['borrowing_id']);
        $setting = FineSetting::active();
        $tanggalDikembalikan = Carbon::parse($data['tanggal_dikembalikan']);
        $tanggalPinjam = Carbon::parse($borrowing->tanggal_pinjam);
        $jatuhTempo = Carbon::parse($borrowing->tanggal_jatuh_tempo);

        if ($tanggalDikembalikan->lt($tanggalPinjam)) {
            throw ValidationException::withMessages([
                'tanggal_dikembalikan' => 'Tanggal pengembalian tidak boleh lebih kecil dari tanggal pinjam.',
            ]);
        }

        $terlambatHari = $tanggalDikembalikan->greaterThan($jatuhTempo)
            ? $jatuhTempo->diffInDays($tanggalDikembalikan)
            : 0;

        $dendaKeterlambatan = $terlambatHari * (int) $setting->denda_per_hari_terlambat;
        $hargaBuku = (float) ($borrowing->book?->harga_buku ?? 0);
        $persenKondisi = match ($data['kondisi_buku'] ?? 'baik') {
            'rusak' => (float) $setting->persen_denda_rusak,
            'hilang' => (float) $setting->persen_denda_hilang,
            default => 0.0,
        };
        $dendaKondisi = ($hargaBuku * $persenKondisi) / 100;
        $denda = $dendaKeterlambatan + $dendaKondisi;

        $data['admin_id'] = Auth::id();
        $data['terlambat_hari'] = $terlambatHari;
        $data['denda'] = $denda;

        return $data;
    }

    protected function afterCreate(): void
    {
        $returnBook = $this->record->load('borrowing.book');
        $borrowing = $returnBook->borrowing;

        if (! $borrowing) {
            return;
        }

        $borrowing->update([
            'admin_id' => Auth::id(),
            'tanggal_kembali' => $returnBook->tanggal_dikembalikan,
            'status' => $returnBook->kondisi_buku === 'hilang' ? 'hilang' : 'dikembalikan',
            'denda' => $returnBook->denda,
        ]);

        if ($returnBook->kondisi_buku !== 'hilang' && $borrowing->book) {
            $borrowing->book->increment('stok');
        }

        Notification::make()
            ->title('Pengembalian berhasil diproses.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
