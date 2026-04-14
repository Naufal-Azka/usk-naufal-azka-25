<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(
                    StudentResource::getModel()::query()
                        ->where('role', User::ROLE_SISWA)
                        ->count()
                ),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn ($query) => $query->where('status_akun', User::STATUS_PENDING))
                ->badge(
                    StudentResource::getModel()::query()
                        ->where('role', User::ROLE_SISWA)
                        ->where('status_akun', User::STATUS_PENDING)
                        ->count()
                )
                ->badgeColor('warning'),

            'aktif' => Tab::make('Aktif')
                ->modifyQueryUsing(fn ($query) => $query->where('status_akun', User::STATUS_AKTIF))
                ->badge(
                    StudentResource::getModel()::query()
                        ->where('role', User::ROLE_SISWA)
                        ->where('status_akun', User::STATUS_AKTIF)
                        ->count()
                )
                ->badgeColor('success'),

            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn ($query) => $query->where('status_akun', User::STATUS_DITOLAK))
                ->badge(
                    StudentResource::getModel()::query()
                        ->where('role', User::ROLE_SISWA)
                        ->where('status_akun', User::STATUS_DITOLAK)
                        ->count()
                )
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'pending';
    }
}