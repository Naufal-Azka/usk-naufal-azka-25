<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', [FrontendController::class, 'dashboard'])->name('dashboard');
    Route::get('/katalog', [FrontendController::class, 'catalog'])->name('catalog.index');
    Route::get('/katalog/{book}', [FrontendController::class, 'showBook'])->name('catalog.show');
    Route::post('/katalog/{book}/pinjam', [FrontendController::class, 'borrow'])->name('catalog.borrow');
    Route::get('/riwayat-peminjaman', [FrontendController::class, 'borrowingHistory'])->name('borrowings.history');
    Route::get('/riwayat-peminjaman/{borrowing}', [FrontendController::class, 'borrowingNote'])->name('borrowings.note');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
