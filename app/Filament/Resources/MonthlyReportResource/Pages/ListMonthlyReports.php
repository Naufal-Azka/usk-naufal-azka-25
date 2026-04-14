<?php

namespace App\Filament\Resources\MonthlyReportResource\Pages;

use App\Filament\Resources\MonthlyReportResource;
use App\Models\Borrowing;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListMonthlyReports extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = MonthlyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action(fn (): StreamedResponse => $this->exportPdf()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return MonthlyReportResource::getWidgets();
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua'),
            'kena_denda' => Tab::make('Kena Denda')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('denda', '>', 0)),
            'tanpa_denda' => Tab::make('Tidak Kena Denda')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('denda', '<=', 0)),
        ];
    }

    private function exportPdf(): StreamedResponse
    {
        $filters = $this->tableFilters;
        $query = Borrowing::query()->with(['user', 'book', 'returnBook']);

        $status = $filters['status']['value'] ?? null;
        $bulan = $filters['bulan_tahun']['bulan'] ?? now()->month;
        $tahun = $filters['bulan_tahun']['tahun'] ?? now()->year;
        $kondisi = $filters['kondisi_buku']['kondisi'] ?? null;

        $query
            ->when(filled($status), fn (Builder $query): Builder => $query->where('status', $status))
            ->when(filled($bulan), fn (Builder $query): Builder => $query->whereMonth('tanggal_pinjam', (int) $bulan))
            ->when(filled($tahun), fn (Builder $query): Builder => $query->whereYear('tanggal_pinjam', (int) $tahun))
            ->when(
                filled($kondisi),
                fn (Builder $query): Builder => $query->whereHas(
                    'returnBook',
                    fn (Builder $query): Builder => $query->where('kondisi_buku', $kondisi)
                )
            );

        $activeTab = $this->activeTab;

        if ($activeTab === 'kena_denda') {
            $query->where('denda', '>', 0);
        }

        if ($activeTab === 'tanpa_denda') {
            $query->where('denda', '<=', 0);
        }

        $records = $query->latest('tanggal_pinjam')->get();

        $pdf = Pdf::loadView('exports.monthly-report', [
            'records' => $records,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'status' => $status,
            'kondisi' => $kondisi,
            'tab' => $activeTab,
            'totalPinjaman' => $records->count(),
            'totalTerlambat' => $records->where('status', 'terlambat')->count(),
            'totalDenda' => $records->sum('denda'),
        ])->setPaper('a4', 'landscape');

        $filename = sprintf('laporan-bulanan-%s-%s.pdf', str_pad((string) $bulan, 2, '0', STR_PAD_LEFT), $tahun);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }
}
