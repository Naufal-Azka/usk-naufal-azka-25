<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BorrowingAndFineChart extends ChartWidget
{
    public static function canView(): bool
    {
        return false;
    }

    protected function getData(): array
    {
        return [
            'datasets' => [],
            'labels' => [],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
