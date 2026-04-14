<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowingResource\Pages;
use App\Models\Borrowing;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BorrowingResource extends Resource
{
    protected static ?string $model = Borrowing::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Peminjaman';
    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?string $pluralModelLabel = 'Peminjaman';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Peminjaman')
                ->schema([
                    Forms\Components\TextInput::make('kode_peminjaman')
                        ->label('Kode Peminjaman')
                        ->default(fn () => 'PMJ-' . strtoupper(Str::random(8)))
                        ->required()
                        ->unique(ignoreRecord: true)
                        
                        ->maxLength(50),

                    Forms\Components\Select::make('user_id')
                        ->label('Siswa')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query
                                ->where('role', User::ROLE_SISWA)
                                ->where('status_akun', User::STATUS_AKTIF)
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('book_id')
                        ->label('Buku')
                        ->relationship(
                            name: 'book',
                            titleAttribute: 'judul',
                            modifyQueryUsing: fn (Builder $query) => $query->where('stok', '>', 0)
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Hanya buku dengan stok > 0 yang dapat dipinjam.'),

                    Forms\Components\DatePicker::make('tanggal_pinjam')
                        ->label('Tanggal Pinjam')
                        ->default(now())
                        ->maxDate(now())
                        ->required(),

                    Forms\Components\DatePicker::make('tanggal_jatuh_tempo')
                        ->label('Tanggal Jatuh Tempo')
                        ->default(now()->addDays(7))
                        ->minDate(fn (Forms\Get $get) => $get('tanggal_pinjam') ?: now()->toDateString())
                        ->required()
                        ->afterOrEqual('tanggal_pinjam'),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'dipinjam' => 'Dipinjam',
                            'dikembalikan' => 'Dikembalikan',
                            'terlambat' => 'Terlambat',
                            'hilang' => 'Hilang',
                        ])
                        ->default('dipinjam')
                        ->disabled()
                        ->dehydrated()
                        ->required(),

                    Forms\Components\TextInput::make('denda')
                        ->label('Denda')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->minValue(0)
                        ->disabled()
                        ->dehydrated()
                        ->required(),

                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_peminjaman')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('book.judul')
                    ->label('Buku')
                    ->searchable()
                    ->sortable()
                    ->limit(35),

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
                    ->color(fn (string $state) => match ($state) {
                        'dipinjam' => 'warning',
                        'dikembalikan' => 'success',
                        'terlambat' => 'danger',
                        'hilang' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('denda')
                    ->label('Denda')
                    ->money('IDR', locale: 'id'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'dipinjam' => 'Dipinjam',
                        'dikembalikan' => 'Dikembalikan',
                        'terlambat' => 'Terlambat',
                        'hilang' => 'Hilang',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Borrowing $record): void {
                        if ($record->status === 'dipinjam' && $record->book) {
                            $record->book->increment('stok');
                        }
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('tanggal_pinjam', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBorrowings::route('/'),
            'create' => Pages\CreateBorrowing::route('/create'),
            'view' => Pages\ViewBorrowing::route('/{record}'),
        ];
    }
}
