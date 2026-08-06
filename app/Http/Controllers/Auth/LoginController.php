<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('username', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Cek status user aktif
            if ($user->status !== 'aktif') {
                Auth::logout();
                return back()->withErrors(['username' => 'Akun Anda tidak aktif. Hubungi administrator.']);
            }

            $request->session()->regenerate();

            // Catat aktivitas login
            AuditLog::create([
                'user_id'    => $user->id,
                'aktivitas'  => 'Login ke sistem',
                'ip_address' => $request->ip(),
            ]);

            return $this->redirectByRole($user->role);
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'aktivitas'  => 'Logout dari sistem',
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(string $role): \Illuminate\Http\RedirectResponse
    {
        return match ($role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'gudang' => redirect()->route('gudang.dashboard'),
            'kasir'  => redirect()->route('kasir.dashboard'),
            default  => redirect()->route('login'),
        };
    }
}
