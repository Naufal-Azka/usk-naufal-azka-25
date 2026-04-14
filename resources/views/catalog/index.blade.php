<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <form method="GET" action="{{ route('catalog.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div class="sm:col-span-3">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari judul, penulis, kode buku, atau ISBN..."
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div class="sm:col-span-1">
                        <select
                            name="category_id"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="rounded-lg bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition sm:col-span-1" type="submit">
                        Cari Buku
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($books as $book)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-3">
                        <div class="aspect-square w-full overflow-hidden rounded-lg bg-slate-100">
                            <img
                                src="{{ $book->cover ? asset('storage/' . $book->cover) : url('/images/no-book-cover.png') }}"
                                alt="Cover {{ $book->judul }}"
                                class="h-full w-full object-cover"
                            >
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">{{ $book->kode_buku }}</p>
                            <h3 class="text-lg font-semibold text-slate-800 leading-snug">{{ $book->judul }}</h3>
                            <p class="text-sm text-gray-600">{{ $book->penulis }}</p>
                        </div>

                        <div class="text-sm text-gray-600 space-y-1">
                            <p>Kategori: <span class="font-medium text-slate-700">{{ $book->category?->name ?? '-' }}</span></p>
                            <p>Stok: <span class="font-medium {{ $book->stok > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $book->stok }}</span></p>
                            <p>Harga Buku: <span class="font-medium text-slate-700">Rp {{ number_format((float) $book->harga_buku, 0, ',', '.') }}</span></p>
                        </div>

                        <div class="pt-2 mt-auto">
                            <a
                                href="{{ route('catalog.show', $book) }}"
                                class="block w-full rounded-lg px-4 py-2 text-sm font-semibold text-center transition bg-amber-500 text-white hover:bg-amber-600"
                            >
                                Lihat Buku
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-500">
                        Buku tidak ditemukan.
                    </div>
                @endforelse
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-sm text-gray-600 text-center">
                    Menampilkan {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} dari {{ $books->total() }} buku
                </p>
                <div class="mt-3 flex justify-center">
                    {{ $books->onEachSide(1)->links() }}
                </div>
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
