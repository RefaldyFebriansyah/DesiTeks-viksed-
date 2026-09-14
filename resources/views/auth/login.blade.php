@extends('layouts.auth')

@section('title', 'Login Portal Supplier — DesiTeks')

@push('styles')
<style>
    .login-page-wrapper {
        min-height: 100vh;
        overflow-y: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px 16px;
        background: 
            linear-gradient(135deg, rgba(15, 23, 42, 0.90) 0%, rgba(30, 41, 59, 0.85) 100%),
            url('{{ asset('images/textile_hero_bg.jpg') }}') center center / cover no-repeat;
    }
    .login-card-box {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.2);
        max-width: 410px;
        width: 100%;
        padding: 32px 30px;
        position: relative;
        transition: padding 0.2s ease;
    }
    .login-logo-img {
        height: 68px;
        max-height: 72px;
        width: auto;
        object-fit: contain;
        transition: height 0.2s ease;
    }

    @media (max-width: 576px) {
        .login-page-wrapper {
            padding: 16px 12px;
            align-items: flex-start;
            padding-top: 32px;
            padding-bottom: 32px;
        }
        .login-card-box {
            padding: 22px 20px;
            border-radius: 16px;
        }
        .login-logo-img {
            height: 54px;
        }
    }
</style>
@endpush

@section('content')
<div class="login-page-wrapper">
    <div class="login-card-box">

        {{-- Logo & Heading --}}
        <div class="text-center mb-3">
            <a href="{{ url('/') }}" title="Kembali ke Beranda" class="text-decoration-none d-inline-block">
                <img src="{{ asset('images/logo_transparent.png') }}" alt="DesiTeks" class="login-logo-img">
            </a>
            <div class="mt-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold" style="font-size: 11px; letter-spacing: 0.3px;">
                    <i class="bi bi-truck me-1"></i> Portal Rekanan Supplier
                </span>
            </div>
            <p style="margin: 6px 0 0; font-size: 12.5px; color: #64748b; line-height: 1.4;">
                Masuk untuk membuat surat jalan online dan memantau penerimaan stok kain di DesiTeks
            </p>
        </div>

        {{-- Flash Info / Warning --}}
        @if(session('info'))
            <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2 border-0 shadow-sm" style="border-radius: 10px; font-size: 12px;">
                <i class="bi bi-info-circle-fill flex-shrink-0 text-primary fs-6"></i>
                <span class="text-dark">{{ session('info') }}</span>
            </div>
        @endif

        {{-- Global Errors --}}
        @if($errors->has('login'))
            <div class="alert alert-danger py-2 px-3 mb-3 d-flex align-items-center gap-2 border-0 shadow-sm" style="border-radius: 10px; font-size: 12px;">
                <i class="bi bi-exclamation-circle-fill flex-shrink-0 text-danger fs-6"></i>
                <span>{{ $errors->first('login') }}</span>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <input type="hidden" name="role" value="supplier">

            <div class="mb-3">
                <label class="form-label mb-1 fw-semibold text-slate-800" for="login" style="font-size: 12px;">
                    Email / Username Akun Supplier <span class="text-danger">*</span>
                </label>
                <div class="position-relative">
                    <input
                        type="text"
                        id="login"
                        name="login"
                        class="form-control {{ $errors->has('login') ? 'is-invalid' : '' }}"
                        value="{{ old('login') }}"
                        placeholder="nama@email.com atau username"
                        autofocus
                        required
                        style="padding-left: 36px; border-radius: 10px; font-size: 13.5px; border-color: #cbd5e1; height: 42px;"
                    >
                    <i class="bi bi-person position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; pointer-events: none;"></i>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label mb-1 fw-semibold text-slate-800" for="password" style="font-size: 12px;">
                    Password <span class="text-danger">*</span>
                </label>
                <div class="position-relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan password akun..."
                        required
                        autocomplete="current-password"
                        style="padding-left: 36px; padding-right: 38px; border-radius: 10px; font-size: 13.5px; border-color: #cbd5e1; height: 42px;"
                    >
                    <i class="bi bi-lock position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; pointer-events: none;"></i>
                    <button type="button" onclick="togglePassword()"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px;">
                        <i class="bi bi-eye" id="eyeIcon" style="font-size: 15px;"></i>
                    </button>
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
                <a href="{{ url('/') }}" class="text-muted text-decoration-none fw-medium" style="font-size: 12px;">
                    <i class="bi bi-arrow-left me-1"></i> Beranda
                </a>
            </div>

            <button type="submit"
                class="btn btn-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2"
                style="height: 44px; font-size: 14px; border-radius: 10px; background: #2563eb; border-color: #2563eb; box-shadow: 0 4px 14px rgba(37,99,235,0.3);">
                <i class="bi bi-box-arrow-in-right fs-5"></i>
                <span>Masuk Portal Supplier</span>
            </button>
        </form>

        {{-- Informasi Akun Supplier & Link Register --}}
        <div class="mt-3 pt-3 border-top text-center" style="font-size: 12.5px; color: #64748b;">
            <span>Belum terdaftar sebagai mitra supplier?</span>
            <div class="mt-1">
                <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none d-inline-flex align-items-center gap-1">
                    <i class="bi bi-person-plus-fill"></i> Buat Akun Supplier Baru
                </a>
            </div>
        </div>

        <div class="text-center mt-2" style="font-size: 11px; color: #94a3b8;">
            © {{ date('Y') }} DesiTeks — B2B Partner Network
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
