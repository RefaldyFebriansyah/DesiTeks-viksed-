<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(
        private TwoFactorService $twoFactorService
    ) {}

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
            'login'    => 'required|string',
            'password' => 'required|string',
            'role'     => 'nullable|string|in:admin,kasir,gudang',
        ], [
            'login.required'    => 'Email atau Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput  = trim($request->input('login'));
        $fieldType   = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $remember    = $request->boolean('remember');

        $query = User::where($fieldType, $loginInput);
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }
        $user = $query->first() ?? User::where($fieldType, $loginInput)->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($request->input('password'), $user->password)) {
            // Cek status user aktif
            if ($user->status !== 'aktif') {
                return back()->withErrors(['login' => 'Akun Anda tidak aktif. Hubungi administrator.'])->withInput($request->only('login', 'role'));
            }

            // Verifikasi role selection jika diisi
            if ($request->filled('role') && $user->role !== $request->input('role')) {
                return back()->withErrors(['role' => 'Role yang dipilih tidak sesuai dengan hak akses akun Anda.'])->withInput($request->only('login', 'role'));
            }

            // Generate/Ensure TOTP Secret Key for user
            if (!$user->two_factor_secret) {
                $user->two_factor_secret = $this->twoFactorService->generateSecretKey();
                $user->save();
            }

            $email = $user->email ?? ($user->username . '@desiteks.com');
            $qrCodeUrl = $this->twoFactorService->getQrCodeImageUrl($email, $user->two_factor_secret);

            // Simpan pending user state ke session untuk verifikasi 2FA
            session([
                'pending_user_id'     => $user->id,
                'pending_user_email'  => $email,
                'pending_remember'    => $remember,
                'pending_2fa_secret'  => $user->two_factor_secret,
                'pending_qr_code_url' => $qrCodeUrl,
            ]);

            return redirect()->route('login.verify-2fa');
        }

        return back()->withErrors([
            'login' => 'Email/Username atau password salah.',
        ])->withInput($request->only('login', 'role'));
    }

    public function show2faForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        if (!session()->has('pending_user_id')) {
            return redirect()->route('login');
        }

        $userId = session('pending_user_id');
        $user   = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $this->twoFactorService->generateSecretKey();
            $user->save();
        }

        $secret    = $user->two_factor_secret;
        $userEmail = $user->email ?? ($user->username . '@desiteks.com');
        $otpUrl    = $this->twoFactorService->getOtpauthUrl($userEmail, $secret);
        $qrCodeUrl = $this->twoFactorService->getQrCodeImageUrl($userEmail, $secret);

        session([
            'pending_2fa_secret'  => $secret,
            'pending_qr_code_url' => $qrCodeUrl,
            'pending_user_email'  => $userEmail,
        ]);

        return view('auth.verify-2fa', compact('secret', 'qrCodeUrl', 'otpUrl', 'userEmail'));
    }

    public function verify2fa(Request $request)
    {
        if (!session()->has('pending_user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'one_time_password' => 'required|string|size:6',
        ], [
            'one_time_password.required' => 'Kode autentikasi 6-digit wajib diisi.',
            'one_time_password.size'     => 'Kode autentikasi harus 6 digit angka.',
        ]);

        $submittedCode = trim($request->input('one_time_password'));
        $secret        = session('pending_2fa_secret');

        // Verifikasi kode 6-digit dengan TwoFactorService (Google Authenticator TOTP)
        if ($this->twoFactorService->verifyCode($secret, $submittedCode)) {
            $userId   = session('pending_user_id');
            $remember = session('pending_remember', false);
            $user     = User::find($userId);

            if (!$user) {
                return redirect()->route('login')->withErrors(['login' => 'User tidak ditemukan.']);
            }

            // Mark 2FA confirmed
            if (!$user->two_factor_confirmed_at) {
                $user->update(['two_factor_confirmed_at' => now()]);
            }

            // Selesaikan Auth login
            Auth::login($user, $remember);
            $request->session()->regenerate();

            // Tentukan active_branch_id
            $branchId = $user->branch_id;
            if (!$branchId) {
                $mainBranch = Branch::where('is_main', true)->first() ?? Branch::first();
                $branchId = $mainBranch?->id;
                if ($branchId) {
                    $user->update(['branch_id' => $branchId]);
                }
            }
            session(['active_branch_id' => $branchId]);

            // Clear 2FA pending session data
            session()->forget(['pending_user_id', 'pending_user_email', 'pending_remember', 'pending_2fa_secret', 'pending_qr_code_url']);

            // Catat aktivitas login
            AuditLog::create([
                'user_id'    => $user->id,
                'aktivitas'  => 'Login ke sistem via Google Authenticator 2FA (Role: ' . ucfirst($user->role) . ', Cabang: ' . ($user->branch?->nama_cabang ?? 'Pusat') . ')',
                'ip_address' => $request->ip(),
            ]);

            return $this->redirectByRole($user->role);
        }

        return back()->withErrors([
            'one_time_password' => 'Kode autentikasi 6-digit salah / kadaluarsa. Pastikan jam HP Anda akurat dan cocokkan kode di aplikasi Authenticator.',
        ]);
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
            'kasir'  => redirect()->route('kasir.sales.pos'),
            'gudang' => redirect()->route('gudang.stocks.index'),
            default  => redirect()->route('login'),
        };
    }
}
