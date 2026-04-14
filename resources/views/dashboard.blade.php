<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Sedang Anda Pinjam</p>
                    <p class="mt-1 text-3xl font-bold text-amber-600">{{ $activeBorrowings->count() }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Buku Terlambat Dikembalikan</p>
                    <p class="mt-1 text-3xl font-bold text-rose-600">{{ $jumlahTerlambat }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Tenggat Paling Dekat</p>
                    @if ($tenggatTerdekat)
                        <p class="mt-1 text-lg font-bold text-slate-800">{{ $tenggatTerdekat->book?->judul ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ optional($tenggatTerdekat->tanggal_jatuh_tempo)->format('d M Y') }}</p>
                    @else
                        <p class="mt-1 text-sm text-gray-500">Tidak ada peminjaman aktif.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-800">Buku yang Anda Pinjam</h3>
                    <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Cari buku</a>
                </div>

                <div class="p-5">
                    @if ($activeBorrowings->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada peminjaman aktif.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b border-gray-100">
                                        <th class="py-2 pe-4">Kode</th>
                                        <th class="py-2 pe-4">Judul</th>
                                        <th class="py-2 pe-4">Pinjam</th>
                                        <th class="py-2 pe-4">Jatuh Tempo</th>
                                        <th class="py-2 pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activeBorrowings as $item)
                                        <tr class="border-b border-gray-50">
                                            <td class="py-3 pe-4 font-medium text-slate-700">{{ $item->kode_peminjaman }}</td>
                                            <td class="py-3 pe-4 text-slate-700">{{ $item->book?->judul ?? '-' }}</td>
                                            <td class="py-3 pe-4 text-gray-600">{{ optional($item->tanggal_pinjam)->format('d M Y') }}</td>
                                            <td class="py-3 pe-4 text-gray-600">{{ optional($item->tanggal_jatuh_tempo)->format('d M Y') }}</td>
                                            <td class="py-3 pe-4">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $item->status === 'terlambat' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-slate-800">Riwayat Pengembalian Terakhir</h3>
                </div>

                <div class="p-5">
                    @if ($historyBorrowings->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada riwayat pengembalian.</p>
                    @else
                        <ul class="space-y-3">
                            @foreach ($historyBorrowings as $item)
                                <li class="flex items-center justify-between rounded-lg border border-gray-100 px-4 py-3">
                                    <div>
                                        <p class="font-medium text-slate-700">{{ $item->book?->judul ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->kode_peminjaman }}</p>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ optional($item->tanggal_kembali)->format('d M Y') }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
