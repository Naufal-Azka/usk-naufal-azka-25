<x-guest-layout>
    <div class="mb-6 text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Perpustakaan Digital</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-800">Selamat Datang Kembali</h1>
        <p class="mt-1 text-sm text-slate-500">Masuk untuk melihat katalog dan aktivitas peminjaman Anda.</p>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white/90 p-6 shadow-sm">
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-amber-600 shadow-sm focus:ring-amber-500" name="remember">
                    <span class="ms-2 text-sm text-slate-600">{{ __('Ingat saya') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-amber-600 hover:text-amber-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <x-primary-button class="w-full justify-center !rounded-xl !bg-amber-500 !py-3 text-sm font-semibold uppercase tracking-wide hover:!bg-amber-600 focus:!bg-amber-600 active:!bg-amber-700">
                {{ __('Masuk') }}
            </x-primary-button>
        </form>
    </div>

    <p class="mt-4 text-center text-xs text-slate-500">
        Belum punya akun siswa?
        <a href="{{ route('register') }}" class="font-semibold text-amber-600 hover:text-amber-700">Daftar di sini</a>.
    </p>
</x-guest-layout>
