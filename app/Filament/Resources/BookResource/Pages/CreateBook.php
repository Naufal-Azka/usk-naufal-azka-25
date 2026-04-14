<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use App\Models\Book;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kode_buku'] = $this->generateBookCode();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private function generateBookCode(): string
    {
        do {
            $code = 'BK-' . strtoupper(Str::random(8));
        } while (Book::query()->where('kode_buku', $code)->exists());

        return $code;
    }
}
