@extends('layouts.auth')

@section('title', 'Daftar Akun Mitra Supplier — DesiTeks')

@push('styles')
<style>
    .register-page-wrapper {
        min-height: 100vh;
        overflow-y: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: 
            linear-gradient(135deg, rgba(15, 23, 42, 0.90) 0%, rgba(30, 41, 59, 0.85) 100%),
            url('{{ asset('images/textile_hero_bg.jpg') }}') center center / cover no-repeat;
    }
    .register-card-box {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.3);
        max-width: 880px;
        width: 100%;
        padding: 20px 30px;
        position: relative;
    }
    .register-column-divider {
        border-right: 1px solid #e2e8f0;
    }
    .reg-logo-img {
        height: 120px;
        width: auto;
        object-fit: contain;
    }

    /* Password Strength Bar */
    .pwd-strength-wrap { margin-top: 4px; }
    .pwd-strength-bar {
        height: 3px;
        border-radius: 3px;
        background: #e2e8f0;
        overflow: hidden;
        margin-bottom: 2px;
    }
    .pwd-strength-bar .bar-fill {
        height: 100%;
        width: 0%;
        border-radius: 3px;
        transition: width 0.3s ease, background 0.3s ease;
    }
    .pwd-strength-label {
        font-size: 10px;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    .pwd-checklist {
        list-style: none;
        padding: 0;
        margin: 4px 0 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2px 6px;
    }
    .pwd-checklist li {
        font-size: 10px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 0;
        transition: color 0.2s;
    }
    .pwd-checklist li.pass { color: #16a34a; }
    .pwd-checklist li i { font-size: 10px; }

    /* Field hint */
    .field-hint {
        font-size: 10px;
        margin-top: 2px;
        color: #94a3b8;
    }
    .field-hint.error { color: #dc2626; }
    .field-hint.success { color: #16a34a; }

    @media (max-width: 991.98px) {
        .register-page-wrapper {
            padding: 16px 12px;
            align-items: flex-start;
        }
        .register-card-box {
            padding: 20px 16px;
            border-radius: 14px;
        }
        .register-column-divider {
            border-right: none !important;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 16px;
            margin-bottom: 12px;
        }
        .reg-logo-img {
            height: 46px;
        }
        .btn-register-submit {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>
@endpush

@section('content')
<div class="register-page-wrapper">
    <div class="register-card-box">

        {{-- Header --}}
        <div class="text-center mb-2">
            <a href="{{ url('/') }}" class="text-decoration-none d-inline-block mb-1">
                <img src="{{ asset('images/logo_transparent.png') }}" alt="DesiTeks" class="reg-logo-img">
            </a>
            <h3 class="fw-bold text-dark mb-0 fs-5" style="letter-spacing: -0.02em;">
                Buat Akun Supplier Baru
            </h3>
            <p class="text-muted small mb-0" style="font-size: 11.5px; max-width: 540px; margin: 0 auto; line-height: 1.4;">
                Isi data perusahaan dan akun login kamu untuk mulai kirim surat jalan ke DesiTeks.
            </p>
        </div>

        {{-- Error --}}
        @if ($errors->any())
            <div class="alert alert-danger py-1.5 px-3 mb-2 border-0 shadow-sm d-flex align-items-center gap-2" style="border-radius: 8px; font-size: 12px;">
                <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0 fs-6"></i>
                <span class="fw-semibold">{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" id="registerForm">
            @csrf

            <div class="row g-3 mb-2">
                {{-- Kolom Kiri: Data Perusahaan --}}
                <div class="col-lg-6 pe-lg-3 register-column-divider">
                    <div class="d-flex align-items-center gap-2 mb-2 pb-1.5 border-bottom">
                        <div class="d-flex align-items-center justify-content-center rounded-2 bg-primary bg-opacity-10 text-primary" style="width: 26px; height: 26px; flex-shrink: 0;">
                            <i class="bi bi-building fs-6"></i>
                        </div>
                        <span class="fw-bold text-dark" style="font-size: 13.5px;">Data Perusahaan</span>
                    </div>

                    {{-- Nama Perusahaan --}}
                    <div class="mb-2">
                        <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                            Nama Perusahaan <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_supplier"
                            class="form-control @error('nama_supplier') is-invalid @enderror"
                            value="{{ old('nama_supplier') }}"
                            placeholder="PT. Tekstil Sejahtera"
                            required
                            style="border-radius: 8px; font-size: 12.5px; height: 36px; border-color: #cbd5e1;">
                        @error('nama_supplier')
                            <div class="invalid-feedback" style="font-size: 10.5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kota & No. Telepon --}}
                    <div class="row g-2 mb-2">
                        <div class="col-sm-6">
                            <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                Kota <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="asal_kota"
                                class="form-control @error('asal_kota') is-invalid @enderror"
                                value="{{ old('asal_kota') }}"
                                placeholder="Bandung"
                                required
                                style="border-radius: 8px; font-size: 12.5px; height: 36px; border-color: #cbd5e1;">
                            @error('asal_kota')
                                <div class="invalid-feedback" style="font-size: 10.5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                No. WhatsApp <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold bg-light border-end-0 px-2" style="font-size: 12px; border-color: #cbd5e1; color: #334155;">+62</span>
                                <input type="text" name="no_telepon" id="noTelepon"
                                    class="form-control border-start-0 @error('no_telepon') is-invalid @enderror"
                                    value="{{ old('no_telepon') }}"
                                    placeholder="81234567890"
                                    required
                                    style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; font-size: 12.5px; height: 36px; border-color: #cbd5e1;">
                            </div>
                            <div class="field-hint" id="phoneHint">Jangan pakai angka 0 di depan</div>
                            @error('no_telepon')
                                <div class="text-danger" style="font-size: 10.5px; margin-top: 2px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                            Alamat Gudang <span class="text-muted fw-normal" style="font-size: 10.5px;">(Opsional)</span>
                        </label>
                        <input type="text" name="alamat"
                            class="form-control @error('alamat') is-invalid @enderror"
                            value="{{ old('alamat') }}"
                            placeholder="Jl. Industri No. 10, Kaw. Rancaekek"
                            style="border-radius: 8px; font-size: 12.5px; height: 36px; border-color: #cbd5e1;">
                        @error('alamat')
                            <div class="invalid-feedback" style="font-size: 10.5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Kolom Kanan: Akun Login --}}
                <div class="col-lg-6 ps-lg-3">
                    <div class="d-flex align-items-center gap-2 mb-2 pb-1.5 border-bottom">
                        <div class="d-flex align-items-center justify-content-center rounded-2 bg-primary bg-opacity-10 text-primary" style="width: 26px; height: 26px; flex-shrink: 0;">
                            <i class="bi bi-person-badge fs-6"></i>
                        </div>
                        <span class="fw-bold text-dark" style="font-size: 13.5px;">Akun Login</span>
                    </div>

                    {{-- Nama PIC --}}
                    <div class="mb-2">
                        <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="Budi Santoso"
                            required
                            style="border-radius: 8px; font-size: 12.5px; height: 36px; border-color: #cbd5e1;">
                        @error('name')
                            <div class="invalid-feedback" style="font-size: 10.5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Username & Email --}}
                    <div class="row g-2 mb-2">
                        <div class="col-sm-6">
                            <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="username"
                                class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username') }}"
                                placeholder="budi_supplier"
                                required
                                style="border-radius: 8px; font-size: 12.5px; height: 36px; border-color: #cbd5e1;">
                            @error('username')
                                <div class="invalid-feedback" style="font-size: 10.5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="emailInput"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="nama@gmail.com"
                                required
                                style="border-radius: 8px; font-size: 12.5px; height: 36px; border-color: #cbd5e1;">
                            <div class="field-hint" id="emailHint">Harus pakai @gmail.com</div>
                            @error('email')
                                <div class="text-danger" style="font-size: 10.5px; margin-top: 2px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-2">
                        <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                            Password <span class="text-danger">*</span>
                        </label>
                        <div class="position-relative">
                            <input type="password" name="password" id="regPassword"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Buat password kamu"
                                required
                                style="border-radius: 8px; font-size: 12.5px; height: 36px; border-color: #cbd5e1; padding-right: 36px;">
                            <button type="button" id="togglePwd"
                                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 2px;">
                                <i class="bi bi-eye" id="pwdEyeIcon" style="font-size: 14px;"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger" style="font-size: 10.5px; margin-top: 2px;">{{ $message }}</div>
                        @enderror

                        {{-- Strength Meter --}}
                        <div class="pwd-strength-wrap">
                            <div class="pwd-strength-bar">
                                <div class="bar-fill" id="pwdBarFill"></div>
                            </div>
                            <div class="pwd-strength-label" id="pwdStrengthLabel" style="color: #94a3b8;">Ketik password untuk lihat kekuatan</div>
                        </div>
                        <ul class="pwd-checklist" id="pwdChecklist">
                            <li id="chkLen"><i class="bi bi-circle"></i> Minimal 8 karakter</li>
                            <li id="chkUpper"><i class="bi bi-circle"></i> Ada huruf besar (A-Z)</li>
                            <li id="chkLower"><i class="bi bi-circle"></i> Ada huruf kecil (a-z)</li>
                            <li id="chkNum"><i class="bi bi-circle"></i> Ada angka (0-9)</li>
                        </ul>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                            Ulangi Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="regPasswordConfirm"
                            class="form-control"
                            placeholder="Ketik ulang password"
                            required
                            style="border-radius: 8px; font-size: 12.5px; height: 36px; border-color: #cbd5e1;">
                        <div class="field-hint" id="confirmHint"></div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="pt-2.5 mt-1 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div style="font-size: 12.5px;">
                    <span class="text-muted">Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none ms-1">Masuk</a>
                </div>

                <button type="submit" id="btnSubmit"
                    class="btn btn-primary fw-bold d-inline-flex align-items-center gap-2 px-3.5 py-1.5 shadow-sm btn-register-submit"
                    style="font-size: 13.5px; border-radius: 8px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none; box-shadow: 0 4px 14px rgba(37,99,235,0.28);">
                    <i class="bi bi-person-check-fill fs-6"></i>
                    <span>Daftar Sekarang</span>
                </button>
            </div>
        </form>

        <div class="text-center mt-2" style="font-size: 11px; color: #94a3b8;">
            © {{ date('Y') }} DesiTeks
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pwd = document.getElementById('regPassword');
    const pwdConfirm = document.getElementById('regPasswordConfirm');
    const barFill = document.getElementById('pwdBarFill');
    const strengthLabel = document.getElementById('pwdStrengthLabel');
    const chkLen = document.getElementById('chkLen');
    const chkUpper = document.getElementById('chkUpper');
    const chkLower = document.getElementById('chkLower');
    const chkNum = document.getElementById('chkNum');
    const confirmHint = document.getElementById('confirmHint');
    const emailInput = document.getElementById('emailInput');
    const emailHint = document.getElementById('emailHint');
    const phoneInput = document.getElementById('noTelepon');
    const phoneHint = document.getElementById('phoneHint');
    const togglePwd = document.getElementById('togglePwd');
    const pwdEyeIcon = document.getElementById('pwdEyeIcon');

    // Toggle password visibility
    togglePwd.addEventListener('click', function() {
        if (pwd.type === 'password') {
            pwd.type = 'text';
            pwdEyeIcon.className = 'bi bi-eye-slash';
        } else {
            pwd.type = 'password';
            pwdEyeIcon.className = 'bi bi-eye';
        }
    });

    // Password strength checker
    pwd.addEventListener('input', function() {
        const val = this.value;
        let score = 0;

        const hasLen = val.length >= 8;
        const hasUpper = /[A-Z]/.test(val);
        const hasLower = /[a-z]/.test(val);
        const hasNum = /\d/.test(val);
        const hasSpecial = /[^A-Za-z0-9]/.test(val);

        function mark(el, ok) {
            if (ok) {
                el.classList.add('pass');
                el.querySelector('i').className = 'bi bi-check-circle-fill';
            } else {
                el.classList.remove('pass');
                el.querySelector('i').className = 'bi bi-circle';
            }
        }

        mark(chkLen, hasLen);
        mark(chkUpper, hasUpper);
        mark(chkLower, hasLower);
        mark(chkNum, hasNum);

        if (hasLen) score++;
        if (hasUpper) score++;
        if (hasLower) score++;
        if (hasNum) score++;
        if (hasSpecial) score++;
        if (val.length >= 12) score++;

        if (val.length === 0) {
            barFill.style.width = '0%';
            barFill.style.background = '#e2e8f0';
            strengthLabel.textContent = 'Ketik password untuk lihat kekuatan';
            strengthLabel.style.color = '#94a3b8';
        } else if (score <= 2) {
            barFill.style.width = '25%';
            barFill.style.background = '#ef4444';
            strengthLabel.textContent = '🔴 Lemah — gampang ditebak';
            strengthLabel.style.color = '#ef4444';
        } else if (score <= 4) {
            barFill.style.width = '60%';
            barFill.style.background = '#f59e0b';
            strengthLabel.textContent = '🟡 Sedang — tambahin variasi';
            strengthLabel.style.color = '#d97706';
        } else {
            barFill.style.width = '100%';
            barFill.style.background = '#16a34a';
            strengthLabel.textContent = '🟢 Kuat — password aman!';
            strengthLabel.style.color = '#16a34a';
        }

        // Also check confirm match
        checkConfirm();
    });

    // Password confirm checker
    function checkConfirm() {
        const val = pwdConfirm.value;
        if (val.length === 0) {
            confirmHint.textContent = '';
            confirmHint.className = 'field-hint';
            return;
        }
        if (val === pwd.value) {
            confirmHint.textContent = '✓ Password cocok';
            confirmHint.className = 'field-hint success';
        } else {
            confirmHint.textContent = '✗ Password belum cocok';
            confirmHint.className = 'field-hint error';
        }
    }
    pwdConfirm.addEventListener('input', checkConfirm);

    // Email validation — must be @gmail.com
    emailInput.addEventListener('input', function() {
        const val = this.value.trim();
        if (val.length === 0) {
            emailHint.textContent = 'Harus pakai @gmail.com';
            emailHint.className = 'field-hint';
            return;
        }
        if (/^[a-zA-Z0-9._%+\-]+@gmail\.com$/i.test(val)) {
            emailHint.textContent = '✓ Email valid';
            emailHint.className = 'field-hint success';
        } else if (val.includes('@') && !val.endsWith('@gmail.com')) {
            emailHint.textContent = '✗ Harus @gmail.com, bukan domain lain';
            emailHint.className = 'field-hint error';
        } else {
            emailHint.textContent = 'Harus pakai @gmail.com';
            emailHint.className = 'field-hint';
        }
    });

    // Phone validation — no leading 0
    phoneInput.addEventListener('input', function() {
        // Strip non-digits
        let val = this.value.replace(/[^0-9]/g, '');
        
        if (val.startsWith('0')) {
            phoneHint.textContent = '✗ Jangan pakai 0 di depan, sudah ada +62';
            phoneHint.className = 'field-hint error';
        } else if (val.length > 0 && val.length < 8) {
            phoneHint.textContent = 'Nomor terlalu pendek';
            phoneHint.className = 'field-hint error';
        } else if (val.length >= 8) {
            phoneHint.textContent = '✓ Nomor valid (+62' + val + ')';
            phoneHint.className = 'field-hint success';
        } else {
            phoneHint.textContent = 'Jangan pakai angka 0 di depan';
            phoneHint.className = 'field-hint';
        }
    });
});
</script>
@endsection

