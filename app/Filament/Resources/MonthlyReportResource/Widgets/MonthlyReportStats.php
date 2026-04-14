<?php

namespace App\Filament\Resources\MonthlyReportResource\Widgets;

use App\Models\Borrowing;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class MonthlyReportStats extends BaseWidget
{
    protected function getStats(): array
    {
        $baseQuery = Borrowing::query();
        $baseQuery = $this->applyTableFilters($baseQuery);

        $totalPinjaman = (clone $baseQuery)->count();
        $totalTerlambat = (clone $baseQuery)->where('status', 'terlambat')->count();
        $totalDenda = (clone $baseQuery)->sum('denda');

        return [
            Stat::make('Total Pinjaman', number_format($totalPinjaman))
                ->description('Jumlah transaksi pada filter aktif')
                ->color('primary'),

            Stat::make('Total Terlambat', number_format($totalTerlambat))
                ->description('Peminjaman berstatus terlambat')
                ->color('danger'),

            Stat::make('Total Denda', 'Rp ' . number_format((float) $totalDenda, 0, ',', '.'))
                ->description('Akumulasi denda pada filter aktif')
                ->color('warning'),
        ];
    }

    private function applyTableFilters(Builder $query): Builder
    {
        $filters = request()->input('tableFilters', []);

        $status = $filters['status']['value'] ?? null;
        $bulan = $filters['bulan_tahun']['bulan'] ?? now()->month;
        $tahun = $filters['bulan_tahun']['tahun'] ?? now()->year;

        return $query
            ->when(filled($status), fn (Builder $query): Builder => $query->where('status', $status))
            ->when(filled($bulan), fn (Builder $query): Builder => $query->whereMonth('tanggal_pinjam', (int) $bulan))
            ->when(filled($tahun), fn (Builder $query): Builder => $query->whereYear('tanggal_pinjam', (int) $tahun));
    }
}
