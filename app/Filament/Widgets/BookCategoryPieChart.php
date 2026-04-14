<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class BookCategoryPieChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Kategori Buku';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $rows = Category::query()
            ->withCount('books')
            ->orderByDesc('books_count')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Buku',
                    'data' => $rows->pluck('books_count')->map(fn ($v) => (int) $v)->all(),
                    'backgroundColor' => ['#0ea5e9', '#f59e0b', '#22c55e', '#ef4444', '#8b5cf6', '#14b8a6', '#f97316', '#6366f1'],
                ],
            ],
            'labels' => $rows->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
