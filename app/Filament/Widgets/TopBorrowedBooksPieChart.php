<?php

namespace App\Filament\Widgets;

use App\Models\Borrowing;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopBorrowedBooksPieChart extends ChartWidget
{
    protected static ?string $heading = 'Buku Paling Banyak Dipinjam';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $rows = Borrowing::query()
            ->select('books.judul', DB::raw('COUNT(*) as total'))
            ->join('books', 'books.id', '=', 'borrowings.book_id')
            ->groupBy('books.id', 'books.judul')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Peminjaman',
                    'data' => $rows->pluck('total')->map(fn ($v) => (int) $v)->all(),
                    'backgroundColor' => ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6'],
                ],
            ],
            'labels' => $rows->pluck('judul')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
