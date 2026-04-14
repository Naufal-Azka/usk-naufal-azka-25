<x-guest-layout>
    <div class="mb-6 text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Pendaftaran Siswa</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-800">Buat Akun Baru</h1>
        <p class="mt-1 text-sm text-slate-500">Setelah daftar, akun akan menunggu persetujuan admin.</p>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white/90 p-6 shadow-sm">
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="name" value="Nama" />
                    <x-text-input id="name" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" type="text" name="name" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" type="email" name="email" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nis" value="NIS" />
                    <x-text-input id="nis" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" type="text" name="nis" :value="old('nis')" required />
                    <x-input-error :messages="$errors->get('nis')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="jurusan" value="Jurusan" />
                    <x-text-input id="jurusan" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" type="text" name="jurusan" :value="old('jurusan')" required />
                    <x-input-error :messages="$errors->get('jurusan')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="kelas" value="Kelas" />
                    <x-text-input id="kelas" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" type="text" name="kelas" :value="old('kelas')" required />
                    <x-input-error :messages="$errors->get('kelas')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
                    <x-text-input id="tanggal_lahir" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" type="date" name="tanggal_lahir" :value="old('tanggal_lahir')" max="{{ now()->toDateString() }}" required />
                    <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="alamat" value="Alamat" />
                <textarea id="alamat" name="alamat" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-amber-500 focus:ring-amber-500">{{ old('alamat') }}</textarea>
                <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="no_hp" value="No HP" />
                <x-text-input id="no_hp" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" type="text" name="no_hp" :value="old('no_hp')" />
                <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" type="password" name="password_confirmation" required />
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a class="text-sm font-medium text-amber-600 hover:text-amber-700" href="{{ route('login') }}">
                    Sudah punya akun?
                </a>

                <x-primary-button class="!rounded-xl !bg-amber-500 px-5 py-2 text-sm font-semibold hover:!bg-amber-600">
                    Daftar
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
