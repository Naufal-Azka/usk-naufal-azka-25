<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LibraryOverviewStats extends BaseWidget
{
    protected ?string $heading = 'Ringkasan Perpustakaan';

    protected function getStats(): array
    {
        $jumlahBuku = Book::query()->count();
        $jumlahSiswa = User::query()->where('role', User::ROLE_SISWA)->count();
        $jumlahPeminjaman = Borrowing::query()->count();

        $bukuBulanIni = Book::query()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $bukuBulanLalu = Book::query()->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();

        $siswaBulanIni = User::query()
            ->where('role', User::ROLE_SISWA)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $siswaBulanLalu = User::query()
            ->where('role', User::ROLE_SISWA)
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->count();

        $pinjamBulanIni = Borrowing::query()
            ->whereBetween('tanggal_pinjam', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->count();
        $pinjamBulanLalu = Borrowing::query()
            ->whereBetween('tanggal_pinjam', [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()])
            ->count();

        [$growthBukuText, $growthBukuIcon, $growthBukuColor] = $this->formatGrowth($bukuBulanIni, $bukuBulanLalu);
        [$growthSiswaText, $growthSiswaIcon, $growthSiswaColor] = $this->formatGrowth($siswaBulanIni, $siswaBulanLalu);
        [$growthPinjamText, $growthPinjamIcon, $growthPinjamColor] = $this->formatGrowth($pinjamBulanIni, $pinjamBulanLalu);

        return [
            Stat::make('Jumlah Buku', number_format($jumlahBuku))
                ->description($growthBukuText)
                ->descriptionIcon($growthBukuIcon)
                ->color($growthBukuColor)
                ->icon('heroicon-m-book-open'),

            Stat::make('Jumlah Siswa', number_format($jumlahSiswa))
                ->description($growthSiswaText)
                ->descriptionIcon($growthSiswaIcon)
                ->color($growthSiswaColor)
                ->icon('heroicon-m-academic-cap'),

            Stat::make('Jumlah Peminjaman', number_format($jumlahPeminjaman))
                ->description($growthPinjamText)
                ->descriptionIcon($growthPinjamIcon)
                ->color($growthPinjamColor)
                ->icon('heroicon-m-clipboard-document-list'),
        ];
    }

    private function formatGrowth(int $current, int $previous): array
    {
        if ($previous === 0) {
            if ($current === 0) {
                return ['0% dari bulan lalu', 'heroicon-m-minus', 'gray'];
            }

            return ['+100% dari bulan lalu', 'heroicon-m-arrow-trending-up', 'success'];
        }

        $percent = (($current - $previous) / $previous) * 100;
        $formatted = number_format(abs($percent), 1);

        if ($percent > 0) {
            return ["+{$formatted}% dari bulan lalu", 'heroicon-m-arrow-trending-up', 'success'];
        }

        if ($percent < 0) {
            return ["-{$formatted}% dari bulan lalu", 'heroicon-m-arrow-trending-down', 'danger'];
        }

        return ['0% dari bulan lalu', 'heroicon-m-minus', 'gray'];
    }
}
