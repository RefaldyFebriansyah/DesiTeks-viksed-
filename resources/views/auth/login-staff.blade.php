@extends('layouts.auth')

@section('title', 'Login Staf Internal — MitraSeratBuana')

@section('content')
<div class="login-page-wrapper" style="
    height: 100vh;
    max-height: 100vh;
    min-height: 100vh;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: 
        linear-gradient(135deg, rgba(8, 15, 30, 0.92) 0%, rgba(15, 23, 42, 0.88) 100%),
        url('{{ asset('images/textile_hero_bg.jpg') }}') center center / cover no-repeat;
">
    <div class="login-card-box" style="
        background: rgba(255, 255, 255, 0.98);
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.2);
        max-width: 410px;
        width: 100%;
        padding: 24px 28px;
        position: relative;
    ">

        {{-- Logo & Heading --}}
        <div class="text-center mb-3">
            <a href="{{ url('/') }}" title="Kembali ke Beranda" class="text-decoration-none d-inline-block">
                <img src="{{ asset('images/logo_transparent.png') }}" alt="MitraSeratBuana"
                    style="height: 72px; max-height: 75px; width: auto; object-fit: contain;">
            </a>
            <div class="mt-2">
                <span class="badge bg-dark text-white rounded-pill px-3 py-1 fw-bold" style="font-size: 11px; letter-spacing: 0.3px;">
                    <i class="bi bi-shield-lock-fill me-1"></i> Portal Staf Internal
                </span>
            </div>
            <p style="margin: 6px 0 0; font-size: 12.5px; color: #64748b; line-height: 1.4;">
                Akses khusus staf Administrator, Kasir POS, dan Manajemen Gudang
            </p>
        </div>

        {{-- Global Errors --}}
        @if($errors->has('login'))
            <div class="alert alert-danger py-2 px-3 mb-2.5 d-flex align-items-center gap-2 border-0 shadow-sm" style="border-radius: 10px; font-size: 12px;">
                <i class="bi bi-exclamation-circle-fill flex-shrink-0 text-danger fs-6"></i>
                <span>{{ $errors->first('login') }}</span>
            </div>
        @endif
        @if($errors->has('role'))
            <div class="alert alert-warning py-2 px-3 mb-2.5 d-flex align-items-center gap-2 border-0 shadow-sm" style="border-radius: 10px; font-size: 12px;">
                <i class="bi bi-shield-exclamation flex-shrink-0 text-warning fs-6"></i>
                <span>{{ $errors->first('role') }}</span>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-2.5">
                <label class="form-label mb-1 fw-semibold text-slate-800" for="login" style="font-size: 12px;">
                    Email / Username Staf <span class="text-danger">*</span>
                </label>
                <div class="position-relative">
                    <input
                        type="text"
                        id="login"
                        name="login"
                        class="form-control {{ $errors->has('login') ? 'is-invalid' : '' }}"
                        value="{{ old('login') }}"
                        placeholder="admin, gudang, atau kasir"
                        autofocus
                        required
                        style="padding-left: 36px; border-radius: 10px; font-size: 13.5px; border-color: #cbd5e1; height: 40px;"
                    >
                    <i class="bi bi-person position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; pointer-events: none;"></i>
                </div>
            </div>

            <div class="mb-2.5">
                <label class="form-label mb-1 fw-semibold text-slate-800" for="password" style="font-size: 12px;">
                    Password <span class="text-danger">*</span>
                </label>
                <div class="position-relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan password..."
                        required
                        autocomplete="current-password"
                        style="padding-left: 36px; padding-right: 38px; border-radius: 10px; font-size: 13.5px; border-color: #cbd5e1; height: 40px;"
                    >
                    <i class="bi bi-lock position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; pointer-events: none;"></i>
                    <button type="button" onclick="togglePassword()"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0;">
                        <i class="bi bi-eye" id="eyeIcon" style="font-size: 15px;"></i>
                    </button>
                </div>
            </div>

            <div class="mb-2.5">
                <label class="form-label mb-1 fw-semibold text-slate-800" for="role" style="font-size: 12px;">
                    Hak Akses / Role (Opsional)
                </label>
                <div class="position-relative">
                    <select name="role" id="role" class="form-select" style="padding-left: 36px; border-radius: 10px; font-size: 13px; border-color: #cbd5e1; height: 40px;">
                        <option value="">Deteksi Otomatis Sesuai Akun</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator (Cabang Utama)</option>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir (POS & Transaksi)</option>
                        <option value="gudang" {{ old('role') === 'gudang' ? 'selected' : '' }}>Gudang (Inventaris & Pengadaan)</option>
                    </select>
                    <i class="bi bi-shield-check position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; pointer-events: none;"></i>
                </div>
            </div>

            <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }} style="cursor: pointer;">
                    <label class="form-check-label" for="remember"
                        style="font-size: 12px; color: #64748b; cursor: pointer;">
                        Ingat saya
                    </label>
                </div>
                <a href="{{ url('/') }}" class="text-muted text-decoration-none" style="font-size: 12px;">
                    <i class="bi bi-arrow-left me-1"></i> Beranda
                </a>
            </div>

            <button type="submit"
                class="btn btn-dark w-100 fw-semibold d-flex align-items-center justify-content-center gap-2"
                style="height: 42px; font-size: 14px; border-radius: 10px; background: #0f172a;">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Masuk ke Sistem Staf</span>
            </button>
        </form>

        {{-- Switch to Supplier Login --}}
        <div class="mt-3 pt-2.5 border-top text-center" style="font-size: 12px;">
            <span class="text-muted">Mitra Rekanan / Supplier Kain?</span>
            <div class="mt-0.5">
                <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none d-inline-flex align-items-center gap-1">
                    <i class="bi bi-truck"></i> Masuk ke Portal Supplier di Sini
                </a>
            </div>
        </div>

        <div class="text-center mt-2" style="font-size: 11px; color: #94a3b8;">
            © {{ date('Y') }} MitraSeratBuana — Internal Enterprise System
        </div>

    </div>
</div>

<script>
function togglePassword() {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        pwd.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endsection
