<x-app-layout>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-800">Nota Pengembalian</h2>
                    <span class="text-sm text-gray-500">{{ $borrowing->kode_peminjaman }}</span>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Judul Buku</p>
                        <p class="font-medium text-slate-700">{{ $borrowing->book?->judul ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="font-medium text-slate-700">{{ ucfirst($borrowing->status) }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Pinjam</p>
                        <p class="font-medium text-slate-700">{{ optional($borrowing->tanggal_pinjam)->format('d M Y') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Jatuh Tempo</p>
                        <p class="font-medium text-slate-700">{{ optional($borrowing->tanggal_jatuh_tempo)->format('d M Y') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Dikembalikan</p>
                        <p class="font-medium text-slate-700">{{ optional($borrowing->tanggal_kembali)->format('d M Y') ?: '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Kondisi Buku</p>
                        <p class="font-medium text-slate-700">{{ ucfirst($borrowing->returnBook?->kondisi_buku ?? '-') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Terlambat</p>
                        <p class="font-medium text-slate-700">{{ (int) ($borrowing->returnBook?->terlambat_hari ?? 0) }} hari</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Total Denda</p>
                        <p class="font-semibold text-rose-600">Rp {{ number_format((float) $borrowing->denda, 0, ',', '.') }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-gray-500">Catatan</p>
                        <p class="font-medium text-slate-700">{{ $borrowing->returnBook?->catatan ?? $borrowing->catatan ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <a href="{{ route('borrowings.history') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">&larr; Kembali ke riwayat</a>
            </div>
        </div>
    </div>
</x-app-layout>
