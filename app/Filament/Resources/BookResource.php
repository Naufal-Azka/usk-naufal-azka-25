<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers\ActiveBorrowingsRelationManager;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Buku';
    protected static ?string $modelLabel = 'Buku';
    protected static ?string $pluralModelLabel = 'Buku';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Buku')
                ->schema([
                    Forms\Components\Select::make('category_id')
                        ->label('Kategori')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('kode_buku')
                        ->label('Kode Buku')
                        ->default(fn () => 'BK-' . strtoupper(Str::random(8)))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->disabled()
                        ->dehydrated(),

                    Forms\Components\TextInput::make('judul')
                        ->label('Judul Buku')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('penulis')
                        ->label('Penulis')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('penerbit')
                        ->label('Penerbit')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('tahun_terbit')
                        ->label('Tahun Terbit')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(date('Y'))
                        ->nullable(),

                    Forms\Components\TextInput::make('isbn')
                        ->label('ISBN')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('jumlah_halaman')
                        ->label('Jumlah Halaman')
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),

                    Forms\Components\TextInput::make('lokasi_rak')
                        ->label('Lokasi Rak')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('stok')
                        ->label('Stok')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->required(),

                    Forms\Components\TextInput::make('harga_buku')
                        ->label('Harga Buku')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->minValue(0)
                        ->required(),

                    Forms\Components\FileUpload::make('cover')
                        ->label('Cover Buku')
                        ->image()
                        ->directory('books/covers')
                        ->imageEditor()
                        ->nullable(),

                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('Cover')
                    ->square()
                    ->defaultImageUrl(url('/images/no-book-cover.png')),

                Tables\Columns\TextColumn::make('kode_buku')
                    ->label('Kode Buku')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('penulis')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('tahun_terbit')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('lokasi_rak')
                    ->label('Rak')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stok')
                    ->label('Stok')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 3 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('harga_buku')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('stok_habis')
                    ->label('Stok Habis')
                    ->query(fn ($query) => $query->where('stok', '<=', 0)),

                Tables\Filters\Filter::make('stok_menipis')
                    ->label('Stok Menipis')
                    ->query(fn ($query) => $query->whereBetween('stok', [1, 3])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('judul');
    }

    public static function getRelations(): array
    {
        return [
            ActiveBorrowingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'view' => Pages\ViewBook::route('/{record}'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
