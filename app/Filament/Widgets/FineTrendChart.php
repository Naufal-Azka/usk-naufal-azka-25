<?php

namespace App\Filament\Widgets;

use App\Models\Borrowing;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class FineTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Total Denda per Bulan (6 Bulan Terakhir)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $months = collect(range(0, 5))
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths(5 - $offset));

        $startDate = $months->first()->copy()->startOfMonth();
        $endDate = $months->last()->copy()->endOfMonth();

        $finesByMonth = Borrowing::query()
            ->selectRaw('YEAR(tanggal_kembali) as year, MONTH(tanggal_kembali) as month, SUM(denda) as total_denda')
            ->whereNotNull('tanggal_kembali')
            ->whereBetween('tanggal_kembali', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupByRaw('YEAR(tanggal_kembali), MONTH(tanggal_kembali)')
            ->get()
            ->mapWithKeys(fn ($item) => [sprintf('%04d-%02d', $item->year, $item->month) => (float) $item->total_denda]);

        $labels = [];
        $values = [];

        foreach ($months as $month) {
            $key = $month->format('Y-m');
            $labels[] = Carbon::parse($month)->translatedFormat('M Y');
            $values[] = (float) ($finesByMonth[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Denda (Rp)',
                    'data' => $values,
                    'borderColor' => '#dc2626',
                    'backgroundColor' => 'rgba(220, 38, 38, 0.2)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
