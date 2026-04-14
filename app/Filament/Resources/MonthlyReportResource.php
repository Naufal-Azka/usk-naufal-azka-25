<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlyReportResource\Pages;
use App\Filament\Resources\MonthlyReportResource\Widgets\MonthlyReportStats;
use App\Models\Borrowing;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class MonthlyReportResource extends Resource
{
    protected static ?string $model = Borrowing::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Bulanan';
    protected static ?string $modelLabel = 'Laporan Bulanan';
    protected static ?string $pluralModelLabel = 'Laporan Bulanan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
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
                    ->limit(35),

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

                Tables\Columns\TextColumn::make('returnBook.kondisi_buku')
                    ->label('Kondisi Buku')
                    ->badge()
                    ->placeholder('-')
                    ->color(fn (?string $state) => match ($state) {
                        'baik' => 'success',
                        'rusak' => 'warning',
                        'hilang' => 'danger',
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

                Filter::make('bulan_tahun')
                    ->form([
                        Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ])
                            ->default((int) now()->month),

                        TextInput::make('tahun')
                            ->label('Tahun')
                            ->numeric()
                            ->default(now()->year),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled($data['bulan'] ?? null),
                                fn (Builder $query): Builder =>
                                    $query->whereMonth('tanggal_pinjam', (int) $data['bulan'])
                            )
                            ->when(
                                filled($data['tahun'] ?? null),
                                fn (Builder $query): Builder =>
                                    $query->whereYear('tanggal_pinjam', (int) $data['tahun'])
                            );
                    }),

                Filter::make('kondisi_buku')
                    ->form([
                        Select::make('kondisi')
                            ->label('Kondisi Buku')
                            ->options([
                                'baik' => 'Baik',
                                'rusak' => 'Rusak',
                                'hilang' => 'Hilang',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['kondisi'] ?? null),
                            fn (Builder $query): Builder => $query->whereHas(
                                'returnBook',
                                fn (Builder $query): Builder => $query->where('kondisi_buku', $data['kondisi'])
                            )
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Borrowing $record): string => static::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([])
            ->defaultSort('tanggal_pinjam', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Laporan Peminjaman')
                    ->schema([
                        TextEntry::make('kode_peminjaman')
                            ->label('Kode Peminjaman'),

                        TextEntry::make('user.name')
                            ->label('Nama Siswa'),

                        TextEntry::make('book.judul')
                            ->label('Judul Buku'),

                        TextEntry::make('tanggal_pinjam')
                            ->label('Tanggal Pinjam')
                            ->date('d M Y'),

                        TextEntry::make('tanggal_jatuh_tempo')
                            ->label('Tanggal Jatuh Tempo')
                            ->date('d M Y'),

                        TextEntry::make('tanggal_kembali')
                            ->label('Tanggal Kembali')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (?string $state) => match ($state) {
                                'dipinjam' => 'warning',
                                'dikembalikan' => 'success',
                                'terlambat' => 'danger',
                                'hilang' => 'gray',
                                default => 'gray',
                            }),

                        TextEntry::make('returnBook.kondisi_buku')
                            ->label('Kondisi Buku')
                            ->badge()
                            ->placeholder('-')
                            ->color(fn (?string $state) => match ($state) {
                                'baik' => 'success',
                                'rusak' => 'warning',
                                'hilang' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('denda')
                            ->label('Denda')
                            ->money('IDR', locale: 'id'),

                        TextEntry::make('catatan')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'book', 'returnBook']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlyReports::route('/'),
            'view' => Pages\ViewMonthlyReport::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            MonthlyReportStats::class,
        ];
    }
}
