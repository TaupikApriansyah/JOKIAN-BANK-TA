<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:5', 'max:100'],
        ]);

        $key = $this->throttleKey($request);
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        $credentials = [
            'email' => Str::lower($request->email),
            'password' => $request->password,
            'status' => 'aktif',
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            $user = $request->user();

            if ($user->role === 'admin') return redirect()->route('admin.dashboard');
            if ($user->role === 'cs') return redirect()->route('cs.dashboard');
            if ($user->role === 'akuntan') return redirect()->route('akuntan.dashboard');

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        RateLimiter::hit($key, 60);
        return back()->withErrors(['email' => 'Email atau password salah, atau akun tidak aktif.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower((string) $request->input('email')) . '|' . $request->ip();
    }
}
