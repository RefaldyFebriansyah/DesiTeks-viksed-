@extends('layouts.auth')

@push('styles')
<style>
    .dt-login-wrap {
        background:
            linear-gradient(135deg, rgba(15, 23, 42, 0.93) 0%, rgba(15, 23, 42, 0.88) 100%),
            url('{{ asset('images/textile_hero_bg.jpg') }}') center center / cover no-repeat !important;
    }
</style>
@endpush

@section('content')
<div class="dt-login-wrap py-2 px-3">
    <div class="dt-login-box" style="max-width: 410px; padding: 20px 22px; margin: auto;">

        {{-- Icon & Header --}}
        <div class="text-center mb-2">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary-soft text-navy rounded-circle mb-1" style="width: 44px; height: 44px; background: rgba(15, 23, 42, 0.06);">
                <i class="bi bi-qr-code-scan text-gold" style="font-size: 22px;"></i>
            </div>
            <h4 class="fw-800 text-navy mb-1" style="font-size: 17px; letter-spacing: -0.3px;">Scan QR Code Authenticator</h4>
            <p class="text-muted m-0" style="font-size: 11.5px; line-height: 1.3;">
                Buka <strong>Google Authenticator</strong> di HP Anda, lalu <strong>Scan QR Code</strong> di bawah.
            </p>
        </div>

        {{-- Account Info --}}
        @if(isset($userEmail))
            <div class="py-1 px-2.5 mb-2 bg-light rounded-3 d-flex align-items-center justify-content-between border" style="font-size: 11px;">
                <span class="text-muted"><i class="bi bi-person-badge me-1"></i> Akun:</span>
                <strong class="text-navy text-truncate" style="max-width: 220px;">{{ $userEmail }}</strong>
            </div>
        @endif

        {{-- QR Code Display Box --}}
        <div class="text-center p-2 mb-2 bg-white rounded-3 border shadow-2xs">
            <div class="mb-1 position-relative d-inline-block">
                <img src="{{ $qrCodeUrl ?? '' }}"
                     onerror="this.onerror=null; this.src='https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' + encodeURIComponent('{{ $otpUrl ?? '' }}');"
                     alt="Scan QR Code Google Authenticator"
                     class="img-fluid rounded border p-1 bg-white"
                     style="width: 135px; height: 135px; object-fit: contain;">
            </div>
            <div class="text-muted" style="font-size: 10.5px;">
                Scan via Google Authenticator HP &bull; Kode Manual:
            </div>
            @if(isset($secret))
                <div class="d-inline-block mt-0.5 px-2 py-0.5 bg-light rounded border font-monospace fw-bold text-navy" style="font-size: 11.5px; letter-spacing: 1.5px;">
                    {{ implode(' ', str_split($secret, 4)) }}
                </div>
            @endif
        </div>

        {{-- Error Alert --}}
        @if($errors->has('one_time_password') || session('error'))
            <div class="dt-alert dt-alert-danger mb-2 py-1.5 px-2.5" style="font-size: 11.5px;">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-1.5"></i>
                <span>{{ $errors->first('one_time_password') ?? session('error') }}</span>
            </div>
        @endif

        {{-- 2FA Verification Form --}}
        <form method="POST" action="{{ route('login.verify-2fa.post') }}">
            @csrf

            <div class="dt-form-group mb-2 text-center">
                <label class="dt-label mb-1 text-navy fw-600" for="one_time_password" style="font-size: 11.5px;">Masukkan 6-Digit Kode dari HP Anda</label>
                <div class="position-relative d-flex justify-content-center">
                    <input
                        type="text"
                        id="one_time_password"
                        name="one_time_password"
                        class="dt-input text-center fw-800 text-navy {{ $errors->has('one_time_password') ? 'is-invalid' : '' }}"
                        placeholder="000000"
                        maxlength="6"
                        pattern="[0-9]*"
                        inputmode="numeric"
                        autofocus
                        required
                        autocomplete="one-time-code"
                        style="font-size: 22px; letter-spacing: 7px; height: 44px; max-width: 210px; border-radius: 9px;"
                    >
                </div>
            </div>

            <button type="submit" class="dt-btn dt-btn-primary w-100 justify-content-center py-2 fw-600" style="font-size: 13.5px; border-radius: 7px; min-height: 40px;">
                <i class="bi bi-shield-check me-1.5"></i> Verifikasi Kode & Masuk
            </button>
        </form>

        <div class="text-center mt-2 pt-1 border-top">
            <a href="{{ route('login') }}" class="text-muted text-decoration-none" style="font-size: 11px;">
                <i class="bi bi-arrow-left me-1"></i> Batal / Kembali ke Login
            </a>
        </div>

    </div>
</div>

<script>
document.getElementById('one_time_password')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endsection
