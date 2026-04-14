<?php

namespace App\Filament\Resources\MonthlyReportResource\Widgets;

use App\Filament\Resources\MonthlyReportResource\Pages\ListMonthlyReports;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;

class MonthlyReportStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListMonthlyReports::class;
    }

    protected function getStats(): array
    {
        $baseQuery = $this->getPageTableQuery();

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
}
