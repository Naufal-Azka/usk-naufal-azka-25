<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 8px 0; }
        .meta { margin-bottom: 14px; }
        .meta p { margin: 2px 0; }
        .stats { margin-bottom: 14px; }
        .stats span { display: inline-block; margin-right: 16px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Laporan Bulanan Peminjaman</h1>

    <div class="meta">
        <p>Periode: {{ $bulan ? str_pad((string) $bulan, 2, '0', STR_PAD_LEFT) : '-' }}/{{ $tahun ?? '-' }}</p>
        <p>Status: {{ $status ? ucfirst($status) : 'Semua' }}</p>
        <p>Kondisi Buku: {{ $kondisi ? ucfirst($kondisi) : 'Semua' }}</p>
        <p>Tab Denda: {{ $tab === 'kena_denda' ? 'Kena Denda' : ($tab === 'tanpa_denda' ? 'Tidak Kena Denda' : 'Semua') }}</p>
    </div>

    <div class="stats">
        <span>Total Pinjaman: {{ $totalPinjaman }}</span>
        <span>Total Terlambat: {{ $totalTerlambat }}</span>
        <span>Total Denda: Rp {{ number_format((float) $totalDenda, 0, ',', '.') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Siswa</th>
                <th>Buku</th>
                <th>Status</th>
                <th>Kondisi</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $row)
                <tr>
                    <td>{{ $row->kode_peminjaman }}</td>
                    <td>{{ $row->user?->name ?? '-' }}</td>
                    <td>{{ $row->book?->judul ?? '-' }}</td>
                    <td>{{ ucfirst($row->status) }}</td>
                    <td>{{ ucfirst($row->returnBook?->kondisi_buku ?? '-') }}</td>
                    <td>{{ optional($row->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>{{ optional($row->tanggal_kembali)->format('d M Y') ?: '-' }}</td>
                    <td>Rp {{ number_format((float) $row->denda, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data pada filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
