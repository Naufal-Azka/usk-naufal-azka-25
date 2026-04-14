<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use App\Models\Book;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['kode_buku'] = $this->record->kode_buku;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn (): bool => ! $this->record->borrowings()->whereIn('status', ['dipinjam', 'terlambat'])->exists())
                ->before(function (): void {
                    /** @var Book $record */
                    $record = $this->record;

                    if ($record->borrowings()->whereIn('status', ['dipinjam', 'terlambat'])->exists()) {
                        throw new \Exception('Buku tidak bisa dihapus karena masih ada data peminjaman aktif.');
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
