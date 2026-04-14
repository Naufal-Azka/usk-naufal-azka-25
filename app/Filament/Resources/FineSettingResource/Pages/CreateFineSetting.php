<?php

namespace App\Filament\Resources\FineSettingResource\Pages;

use App\Filament\Resources\FineSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFineSetting extends CreateRecord
{
    protected static string $resource = FineSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
