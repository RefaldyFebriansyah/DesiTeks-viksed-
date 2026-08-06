@extends('layouts.auth')

@section('content')
<div class="dt-login-wrap">
    <div class="dt-login-box">

        {{-- Logo --}}
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo_transparent.png') }}" alt="DesiTeks"
                style="max-width:120px;height:auto">
            <p style="margin:10px 0 0;font-size:12px;color:var(--dt-muted)">Sistem Manajemen Toko Kain</p>
        </div>

        {{-- Error --}}
        @if($errors->has('username'))
            <div class="dt-alert dt-alert-danger mb-3">
                <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                <span>{{ $errors->first('username') }}</span>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="dt-form-group">
                <label class="dt-label" for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="dt-input {{ $errors->has('username') ? 'is-invalid' : '' }}"
                    value="{{ old('username') }}"
                    placeholder="Masukkan username..."
                    autofocus
                    autocomplete="username"
                >
            </div>

            <div class="dt-form-group">
                <label class="dt-label" for="password">Password</label>
                <div style="position:relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="dt-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan password..."
                        autocomplete="current-password"
                        style="padding-right:42px"
                    >
                    <button type="button" onclick="togglePassword()"
                        style="position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--dt-muted);cursor:pointer;padding:0">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
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
                style="padding:11px;font-size:14.5px">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>

        <div class="text-center mt-4" style="font-size:11px;color:var(--dt-muted)">
            © {{ date('Y') }} DesiTeks
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
