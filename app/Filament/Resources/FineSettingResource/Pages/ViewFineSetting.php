<?php

namespace App\Filament\Resources\FineSettingResource\Pages;

use App\Filament\Resources\FineSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFineSetting extends ViewRecord
{
    protected static string $resource = FineSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
