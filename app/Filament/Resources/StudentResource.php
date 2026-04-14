<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Siswa';
    protected static ?string $modelLabel = 'Siswa';
    protected static ?string $pluralModelLabel = 'Siswa';
    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', User::ROLE_SISWA)
            ->with('student');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Akun')
                ->schema([
                    Forms\Components\Hidden::make('role')
                        ->default(User::ROLE_SISWA),

                    Forms\Components\TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\Select::make('status_akun')
                        ->label('Status Akun')
                        ->options([
                            User::STATUS_PENDING => 'Pending',
                            User::STATUS_AKTIF => 'Aktif',
                            User::STATUS_DITOLAK => 'Ditolak',
                        ])
                        ->default(User::STATUS_PENDING)
                        ->required(),

                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->same('passwordConfirmation'),

                    Forms\Components\TextInput::make('passwordConfirmation')
                        ->label('Konfirmasi Password')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(false),
                ])
                ->columns(2),

            Forms\Components\Section::make('Data Siswa')
                ->relationship('student')
                ->schema([
                    Forms\Components\TextInput::make('nis')
                        ->label('NIS')
                        ->required()
                        ->unique(
                            table: 'students',
                            column: 'nis',
                            ignoreRecord: true
                        )
                        ->maxLength(50),

                    Forms\Components\TextInput::make('jurusan')
                        ->label('Jurusan')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('kelas')
                        ->label('Kelas')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->required(),

                    Forms\Components\Textarea::make('alamat')
                        ->label('Alamat')
                        ->rows(3),

                    Forms\Components\TextInput::make('no_hp')
                        ->label('No HP')
                        ->maxLength(20),

                    Forms\Components\Select::make('status')
                        ->label('Status Siswa')
                        ->options([
                            Student::STATUS_AKTIF => 'Aktif',
                            Student::STATUS_LULUS => 'Lulus',
                            Student::STATUS_KELUAR => 'Keluar',
                        ])
                        ->default(Student::STATUS_AKTIF)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.jurusan')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.kelas')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.status')
                    ->label('Status Siswa')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        Student::STATUS_AKTIF => 'success',
                        Student::STATUS_LULUS => 'info',
                        Student::STATUS_KELUAR => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status_akun')
                    ->label('Status Akun')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        User::STATUS_PENDING => 'warning',
                        User::STATUS_AKTIF => 'success',
                        User::STATUS_DITOLAK => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_akun')
                    ->label('Status Akun')
                    ->options([
                        User::STATUS_PENDING => 'Pending',
                        User::STATUS_AKTIF => 'Aktif',
                        User::STATUS_DITOLAK => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('student_status')
                    ->label('Status Siswa')
                    ->relationship('student', 'status')
                    ->options([
                        Student::STATUS_AKTIF => 'Aktif',
                        Student::STATUS_LULUS => 'Lulus',
                        Student::STATUS_KELUAR => 'Keluar',
                    ]),

                Tables\Filters\SelectFilter::make('jurusan')
                    ->label('Jurusan')
                    ->relationship('student', 'jurusan'),

                Tables\Filters\SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->relationship('student', 'kelas'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => $record->status_akun === User::STATUS_PENDING)
                    ->action(function (User $record): void {
                        $record->update([
                            'status_akun' => User::STATUS_AKTIF,
                        ]);

                        Notification::make()
                            ->title('Akun siswa berhasil disetujui.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => in_array($record->status_akun, [User::STATUS_PENDING], true))
                    ->action(function (User $record): void {
                        $record->update([
                            'status_akun' => User::STATUS_DITOLAK,
                        ]);

                        Notification::make()
                            ->title('Akun siswa ditolak.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('activate')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => $record->status_akun === User::STATUS_DITOLAK)
                    ->action(function (User $record): void {
                        $record->update([
                            'status_akun' => User::STATUS_AKTIF,
                        ]);

                        Notification::make()
                            ->title('Akun siswa berhasil diaktifkan kembali.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
