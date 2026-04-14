<?php

namespace App\Filament\Resources\MonthlyReportResource\Pages;

use App\Filament\Resources\MonthlyReportResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMonthlyReports extends ListRecords
{
    protected static string $resource = MonthlyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return MonthlyReportResource::getWidgets();
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua'),
            'kena_denda' => Tab::make('Kena Denda')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('denda', '>', 0)),
            'tanpa_denda' => Tab::make('Tidak Kena Denda')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('denda', '<=', 0)),
        ];
    }
}
