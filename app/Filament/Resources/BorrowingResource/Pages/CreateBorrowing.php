<?php

namespace App\Filament\Resources\BorrowingResource\Pages;

use App\Filament\Resources\BorrowingResource;
use App\Models\Book;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBorrowing extends CreateRecord
{
    protected static string $resource = BorrowingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Auth::id();
        $data['status'] = 'dipinjam';
        $data['denda'] = 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        $book = Book::query()->find($this->record->book_id);

        if ($book && $book->stok > 0) {
            $book->decrement('stok');
        }

        Notification::make()
            ->title('Data peminjaman berhasil ditambahkan.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
