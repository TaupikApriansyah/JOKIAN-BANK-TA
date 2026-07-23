<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) return redirect('/login');

        $user = Auth::user();
        if ($user->status !== 'aktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->withErrors(['email' => 'Akun Anda sedang tidak aktif.']);
        }

        if (!in_array($user->role, $roles, true)) abort(403, 'ANDA TIDAK PUNYA AKSES');
        return $next($request);
    }
}
