<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturnBookResource\Pages;
use App\Models\Borrowing;
use App\Models\FineSetting;
use App\Models\ReturnBook;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReturnBookResource extends Resource
{
    protected static ?string $model = ReturnBook::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Pengembalian';
    protected static ?string $modelLabel = 'Pengembalian';
    protected static ?string $pluralModelLabel = 'Pengembalian';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Pengembalian')
                ->schema([
                    Forms\Components\Select::make('borrowing_id')
                        ->label('Peminjaman')
                        ->relationship(
                            name: 'borrowing',
                            titleAttribute: 'kode_peminjaman',
                            modifyQueryUsing: fn (Builder $query) => $query
                                ->whereIn('status', ['dipinjam', 'terlambat'])
                                ->whereDoesntHave('returnBook')
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn (Borrowing $record): string => $record->kode_peminjaman . ' - ' . $record->user?->name . ' - ' . $record->book?->judul
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::updateDendaPreview($get, $set))
                        ->required(),

                    Forms\Components\DatePicker::make('tanggal_dikembalikan')
                        ->label('Tanggal Dikembalikan')
                        ->default(now())
                        ->disabled(fn (Get $get): bool => blank($get('borrowing_id')))
                        ->minDate(function (Get $get) {
                            $borrowingId = $get('borrowing_id');

                            if (! $borrowingId) {
                                return null;
                            }

                            return Borrowing::query()->whereKey($borrowingId)->value('tanggal_pinjam');
                        })
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::updateDendaPreview($get, $set))
                        ->rule(function (Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get): void {
                                $borrowingId = $get('borrowing_id');

                                if (! $borrowingId || ! $value) {
                                    return;
                                }

                                $borrowing = Borrowing::query()->find($borrowingId);

                                if (! $borrowing || ! $borrowing->tanggal_pinjam) {
                                    return;
                                }

                                if (\Carbon\Carbon::parse($value)->lt(\Carbon\Carbon::parse($borrowing->tanggal_pinjam))) {
                                    $fail('Tanggal pengembalian tidak boleh lebih kecil dari tanggal pinjam.');
                                }
                            };
                        })
                        ->required(),

                    Forms\Components\Select::make('kondisi_buku')
                        ->label('Kondisi Buku')
                        ->options([
                            'baik' => 'Baik',
                            'rusak' => 'Rusak',
                            'hilang' => 'Hilang',
                        ])
                        ->default('baik')
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::updateDendaPreview($get, $set))
                        ->required(),

                    Forms\Components\TextInput::make('terlambat_hari')
                        ->label('Terlambat (hari)')
                        ->numeric()
                        ->default(0)
                        ->disabled()
                        ->dehydrated(),

                    Forms\Components\TextInput::make('denda')
                        ->label('Denda')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->disabled()
                        ->dehydrated(),

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
                Tables\Columns\TextColumn::make('borrowing.kode_peminjaman')
                    ->label('Kode Pinjam')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('borrowing.user.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('borrowing.book.judul')
                    ->label('Buku')
                    ->searchable()
                    ->limit(35),

                Tables\Columns\TextColumn::make('tanggal_dikembalikan')
                    ->label('Tanggal Kembali')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('terlambat_hari')
                    ->label('Terlambat')
                    ->suffix(' hari'),

                Tables\Columns\TextColumn::make('kondisi_buku')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'baik' => 'success',
                        'rusak' => 'warning',
                        'hilang' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('denda')
                    ->label('Denda')
                    ->money('IDR', locale: 'id'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('tanggal_dikembalikan', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReturnBooks::route('/'),
            'create' => Pages\CreateReturnBook::route('/create'),
            'view' => Pages\ViewReturnBook::route('/{record}'),
        ];
    }

    protected static function updateDendaPreview(Get $get, Set $set): void
    {
        $borrowingId = $get('borrowing_id');
        $tanggalDikembalikan = $get('tanggal_dikembalikan');

        if (! $borrowingId || ! $tanggalDikembalikan) {
            $set('terlambat_hari', 0);
            $set('denda', 0);

            return;
        }

        $borrowing = Borrowing::query()->with('book')->find($borrowingId);

        if (! $borrowing) {
            $set('terlambat_hari', 0);
            $set('denda', 0);

            return;
        }

        $setting = FineSetting::active();
        $tanggalKembali = Carbon::parse($tanggalDikembalikan);
        $jatuhTempo = Carbon::parse($borrowing->tanggal_jatuh_tempo);

        $terlambatHari = $tanggalKembali->greaterThan($jatuhTempo)
            ? $jatuhTempo->diffInDays($tanggalKembali)
            : 0;

        $hargaBuku = (float) ($borrowing->book?->harga_buku ?? 0);
        $persenKondisi = match ($get('kondisi_buku') ?? 'baik') {
            'rusak' => (float) $setting->persen_denda_rusak,
            'hilang' => (float) $setting->persen_denda_hilang,
            default => 0.0,
        };

        $denda = ($terlambatHari * (int) $setting->denda_per_hari_terlambat) + (($hargaBuku * $persenKondisi) / 100);

        $set('terlambat_hari', $terlambatHari);
        $set('denda', round($denda, 2));
    }
}
