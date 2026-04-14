<?php

namespace App\Filament\Resources\FineSettingResource\Pages;

use App\Filament\Resources\FineSettingResource;
use App\Models\FineSetting;
use Filament\Resources\Pages\ListRecords;

class ListFineSettings extends ListRecords
{
    protected static string $resource = FineSettingResource::class;

    public function mount(): void
    {
        parent::mount();

        FineSetting::active();

        $setting = FineSetting::query()->first();

        if ($setting) {
            $this->redirect(FineSettingResource::getUrl('view', ['record' => $setting]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
