<?php

namespace App\Filament\Widgets;

use App\Models\Borrowing;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class BorrowingTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Peminjaman per Bulan (6 Bulan Terakhir)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect(range(0, 5))
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths(5 - $offset));

        $startDate = $months->first()->copy()->startOfMonth();
        $endDate = $months->last()->copy()->endOfMonth();

        $borrowingsByMonth = Borrowing::query()
            ->selectRaw('YEAR(tanggal_pinjam) as year, MONTH(tanggal_pinjam) as month, COUNT(*) as total')
            ->whereBetween('tanggal_pinjam', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupByRaw('YEAR(tanggal_pinjam), MONTH(tanggal_pinjam)')
            ->get()
            ->mapWithKeys(fn ($item) => [sprintf('%04d-%02d', $item->year, $item->month) => (int) $item->total]);

        $labels = [];
        $values = [];

        foreach ($months as $month) {
            $key = $month->format('Y-m');
            $labels[] = Carbon::parse($month)->translatedFormat('M Y');
            $values[] = $borrowingsByMonth[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Peminjaman',
                    'data' => $values,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
