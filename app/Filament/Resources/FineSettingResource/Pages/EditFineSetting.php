<?php

namespace App\Filament\Resources\FineSettingResource\Pages;

use App\Filament\Resources\FineSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFineSetting extends EditRecord
{
    protected static string $resource = FineSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(false),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
