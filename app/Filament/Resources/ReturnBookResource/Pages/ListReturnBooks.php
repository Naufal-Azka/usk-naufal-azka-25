<?php

namespace App\Filament\Resources\ReturnBookResource\Pages;

use App\Filament\Resources\ReturnBookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReturnBooks extends ListRecords
{
    protected static string $resource = ReturnBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
