<?php

namespace App\Filament\Resources\BookResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActiveBorrowingsRelationManager extends RelationManager
{
    protected static string $relationship = 'borrowings';

    protected static ?string $title = 'Data Peminjaman Aktif Buku Ini';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('kode_peminjaman')
            ->modifyQueryUsing(fn ($query) => $query->where('status', 'dipinjam'))
            ->columns([
                Tables\Columns\TextColumn::make('kode_peminjaman')
                    ->label('Kode Peminjaman')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dipinjam' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
