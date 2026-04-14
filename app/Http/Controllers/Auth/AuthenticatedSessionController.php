<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user && $user->role === User::ROLE_ADMIN) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => 'Akun anda tidak terdaftar di data kami',
            ]);
        }

        if ($user && $user->status_akun !== User::STATUS_AKTIF) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => match ($user->status_akun) {
                    User::STATUS_PENDING => 'Akun Anda masih menunggu persetujuan admin.',
                    User::STATUS_DITOLAK => 'Akun Anda ditolak admin. Silakan hubungi admin perpustakaan.',
                    default => 'Akun Anda tidak aktif.',
                },
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
