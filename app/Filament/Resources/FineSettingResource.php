<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FineSettingResource\Pages;
use App\Models\FineSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FineSettingResource extends Resource
{
    protected static ?string $model = FineSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Aturan Denda';
    protected static ?string $modelLabel = 'Aturan Denda';
    protected static ?string $pluralModelLabel = 'Aturan Denda';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Pengaturan Denda')
                ->schema([
                    Forms\Components\TextInput::make('denda_per_hari_terlambat')
                        ->label('Denda per Hari Terlambat')
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(0)
                        ->required(),

                    Forms\Components\TextInput::make('persen_denda_rusak')
                        ->label('Persentase Denda Buku Rusak')
                        ->numeric()
                        ->suffix('%')
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),

                    Forms\Components\TextInput::make('persen_denda_hilang')
                        ->label('Persentase Denda Buku Hilang')
                        ->numeric()
                        ->suffix('%')
                        ->minValue(0)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('denda_per_hari_terlambat')
                    ->label('Denda/Hari')
                    ->money('IDR', locale: 'id'),

                Tables\Columns\TextColumn::make('persen_denda_rusak')
                    ->label('Rusak')
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('persen_denda_hilang')
                    ->label('Hilang')
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Aturan Denda Aktif')
                    ->schema([
                        TextEntry::make('denda_per_hari_terlambat')
                            ->label('Denda per Hari Terlambat')
                            ->money('IDR', locale: 'id'),

                        TextEntry::make('persen_denda_rusak')
                            ->label('Persentase Denda Buku Rusak')
                            ->suffix('%'),

                        TextEntry::make('persen_denda_hilang')
                            ->label('Persentase Denda Buku Hilang')
                            ->suffix('%'),

                        TextEntry::make('updated_at')
                            ->label('Terakhir Diubah')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFineSettings::route('/'),
            'view' => Pages\ViewFineSetting::route('/{record}'),
            'create' => Pages\CreateFineSetting::route('/create'),
            'edit' => Pages\EditFineSetting::route('/{record}/edit'),
        ];
    }
}
