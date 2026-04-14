<x-app-layout>
    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <div class="aspect-[3/4] w-full overflow-hidden rounded-lg bg-slate-100">
                        <img
                            src="{{ $book->cover ? asset('storage/' . $book->cover) : url('/images/no-book-cover.png') }}"
                            alt="Cover {{ $book->judul }}"
                            class="h-full w-full object-cover"
                        >
                    </div>
                </div>

                <div class="md:col-span-2 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500">{{ $book->kode_buku }}</p>
                        <h1 class="text-2xl font-bold text-slate-800">{{ $book->judul }}</h1>
                        <p class="text-sm text-gray-600">Penulis: {{ $book->penulis }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <p class="text-gray-600">Kategori: <span class="font-medium text-slate-700">{{ $book->category?->name ?? '-' }}</span></p>
                        <p class="text-gray-600">Penerbit: <span class="font-medium text-slate-700">{{ $book->penerbit ?? '-' }}</span></p>
                        <p class="text-gray-600">Tahun Terbit: <span class="font-medium text-slate-700">{{ $book->tahun_terbit ?? '-' }}</span></p>
                        <p class="text-gray-600">ISBN: <span class="font-medium text-slate-700">{{ $book->isbn ?? '-' }}</span></p>
                        <p class="text-gray-600">Jumlah Halaman: <span class="font-medium text-slate-700">{{ $book->jumlah_halaman ?? '-' }}</span></p>
                        <p class="text-gray-600">Lokasi Rak: <span class="font-medium text-slate-700">{{ $book->lokasi_rak }}</span></p>
                        <p class="text-gray-600">Stok: <span class="font-medium {{ $book->stok > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $book->stok }}</span></p>
                        <p class="text-gray-600">Harga Buku: <span class="font-medium text-slate-700">Rp {{ number_format((float) $book->harga_buku, 0, ',', '.') }}</span></p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-slate-700">Deskripsi</p>
                        <p class="mt-1 text-sm text-gray-600 leading-relaxed">{{ $book->deskripsi ?: '-' }}</p>
                    </div>

                    @if ($canBorrow)
                        <form
                            method="POST"
                            action="{{ route('catalog.borrow', $book) }}"
                            class="rounded-lg border border-amber-100 bg-amber-50 p-4 space-y-3"
                            onsubmit="return confirm('Yakin ingin meminjam buku ini?');"
                        >
                            @csrf

                            <div>
                                <label for="durasi_hari" class="block text-sm font-medium text-slate-700">Durasi Peminjaman (hari)</label>
                                <input
                                    id="durasi_hari"
                                    name="durasi_hari"
                                    type="number"
                                    min="1"
                                    max="30"
                                    value="{{ old('durasi_hari', $defaultDuration) }}"
                                    class="mt-1 block w-full sm:w-48 rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500"
                                    required
                                >
                                <p class="mt-1 text-xs text-gray-500">Default 7 hari, maksimal 30 hari.</p>
                                @error('durasi_hari')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                @disabled($book->stok <= 0)
                                class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $book->stok > 0 ? 'bg-amber-500 text-white hover:bg-amber-600' : 'bg-gray-200 text-gray-500 cursor-not-allowed' }}"
                            >
                                {{ $book->stok > 0 ? 'Pinjam Buku Ini' : 'Stok Habis' }}
                            </button>
                        </form>
                    @else
                        <div class="rounded-lg bg-slate-100 text-slate-600 text-sm px-4 py-3">
                            {{ $borrowRestrictionMessage }}
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">&larr; Kembali ke katalog</a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            alert(@js(session('success')));
        </script>
    @endif

    @if (session('error'))
        <script>
            alert(@js(session('error')));
        </script>
    @endif
</x-app-layout>
