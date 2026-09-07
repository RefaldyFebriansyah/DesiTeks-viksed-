@extends('layouts.auth')

@section('content')
<div class="dt-login-wrap">
    <div class="dt-login-box">

        {{-- Logo --}}
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo_transparent.png') }}" alt="DesiTeks"
                style="max-width:130px;height:auto">
            <p style="margin:8px 0 0;font-size:12px;color:var(--dt-muted)">Sistem Manajemen Toko Kain</p>
        </div>

        {{-- Global Errors --}}
        @if($errors->has('login'))
            <div class="dt-alert dt-alert-danger mb-3">
                <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                <span>{{ $errors->first('login') }}</span>
            </div>
        @endif
        @if($errors->has('role'))
            <div class="dt-alert dt-alert-warning mb-3">
                <i class="bi bi-shield-exclamation flex-shrink-0"></i>
                <span>{{ $errors->first('role') }}</span>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="dt-form-group">
                <label class="dt-label" for="login">Alamat Email Terdaftar <span class="required">*</span></label>
                <div class="position-relative">
                    <input
                        type="email"
                        id="login"
                        name="login"
                        class="dt-input {{ $errors->has('login') ? 'is-invalid' : '' }}"
                        value="{{ old('login') }}"
                        placeholder="nama@email.com"
                        autofocus
                        required
                        autocomplete="email"
                        style="padding-left:36px;"
                    >
                    <i class="bi bi-envelope position-absolute text-muted" style="left:12px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none;"></i>
                </div>
            </div>

            <div class="dt-form-group">
                <label class="dt-label" for="password">Password <span class="required">*</span></label>
                <div class="position-relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="dt-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan password..."
                        required
                        autocomplete="current-password"
                        style="padding-left:36px; padding-right:42px;"
                    >
                    <i class="bi bi-lock position-absolute text-muted" style="left:12px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none;"></i>
                    <button type="button" onclick="togglePassword()"
                        style="position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--dt-muted);cursor:pointer;padding:0">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="dt-form-group">
                <label class="dt-label" for="role">Role Selection (Opsional)</label>
                <div class="position-relative">
                    <select name="role" id="role" class="dt-select" style="padding-left:36px;">
                        <option value="">Deteksi Otomatis Role Sesuai Akun</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator (Cabang Utama)</option>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir (POS & Transaksi)</option>
                        <option value="gudang" {{ old('role') === 'gudang' ? 'selected' : '' }}>Gudang (Inventaris & Stok)</option>
                    </select>
                    <i class="bi bi-shield-check position-absolute text-muted" style="left:12px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none;"></i>
                </div>
            </div>

            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember"
                        style="font-size:13px;color:var(--dt-muted);cursor:pointer">
                        Ingat saya
                    </label>
                </div>
            </div>

            <button type="submit"
                class="dt-btn dt-btn-primary w-100 justify-content-center"
                style="padding:11px;font-size:14.5px;border-radius:8px;">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
            </button>
        </form>

        <div class="text-center mt-4" style="font-size:11px;color:var(--dt-muted)">
            © {{ date('Y') }} DesiTeks — Multi-Branch Inventory System
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
