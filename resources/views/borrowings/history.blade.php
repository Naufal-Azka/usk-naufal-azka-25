<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <form method="GET" action="{{ route('borrowings.history') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="sm:col-span-3">
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="dipinjam" @selected(request('status') === 'dipinjam')>Dipinjam</option>
                            <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>Dikembalikan</option>
                            <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat</option>
                            <option value="hilang" @selected(request('status') === 'hilang')>Hilang</option>
                        </select>
                    </div>
                    <button class="rounded-lg bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition" type="submit">
                        Filter
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-slate-800">Riwayat Peminjaman Buku</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-100">
                                <th class="py-3 px-5">Kode</th>
                                <th class="py-3 px-5">Judul Buku</th>
                                <th class="py-3 px-5">Tanggal Pinjam</th>
                                <th class="py-3 px-5">Jatuh Tempo</th>
                                <th class="py-3 px-5">Tanggal Kembali</th>
                                <th class="py-3 px-5">Status</th>
                                <th class="py-3 px-5">Kondisi Buku</th>
                                <th class="py-3 px-5">Denda</th>
                                <th class="py-3 px-5">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($history as $item)
                                <tr class="border-b border-gray-50">
                                    <td class="py-3 px-5 font-medium text-slate-700">{{ $item->kode_peminjaman }}</td>
                                    <td class="py-3 px-5 text-slate-700">{{ $item->book?->judul ?? '-' }}</td>
                                    <td class="py-3 px-5 text-gray-600">{{ optional($item->tanggal_pinjam)->format('d M Y') }}</td>
                                    <td class="py-3 px-5 text-gray-600">{{ optional($item->tanggal_jatuh_tempo)->format('d M Y') }}</td>
                                    <td class="py-3 px-5 text-gray-600">{{ optional($item->tanggal_kembali)->format('d M Y') ?: '-' }}</td>
                                    <td class="py-3 px-5">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            {{ $item->status === 'dikembalikan' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                            {{ $item->status === 'terlambat' ? 'bg-rose-100 text-rose-700' : '' }}
                                            {{ $item->status === 'dipinjam' ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $item->status === 'hilang' ? 'bg-gray-200 text-gray-700' : '' }}
                                        ">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-5 text-gray-700">{{ ucfirst($item->returnBook?->kondisi_buku ?? '-') }}</td>
                                    <td class="py-3 px-5 text-gray-700">Rp {{ number_format((float) $item->denda, 0, ',', '.') }}</td>
                                    <td class="py-3 px-5">
                                        <a href="{{ route('borrowings.note', $item) }}" class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-10 px-5 text-center text-gray-500">Belum ada riwayat peminjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-sm text-gray-600 text-center">
                    Menampilkan {{ $history->firstItem() ?? 0 }} - {{ $history->lastItem() ?? 0 }} dari {{ $history->total() }} riwayat
                </p>
                <div class="mt-3 flex justify-center">
                    {{ $history->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
