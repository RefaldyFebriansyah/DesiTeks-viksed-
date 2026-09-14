@extends('layouts.app')
@section('title', 'Detail Pengguna (Read-Only)')
@section('page-title', 'Detail Pengguna Sistem')

@section('content')
<div class="dt-page-header mb-4">
    <div>
        <h1 class="dt-page-title">Detail Pengguna: {{ $user->name }}</h1>
        <div class="dt-breadcrumb">Sistem / Pengguna / Detail (Read-Only)</div>
    </div>
    <a href="{{ route('admin.users.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-start gap-3 p-3.5 mb-4" style="background: #fffbeb; border-left: 4px solid #d97706 !important;">
            <i class="bi bi-info-circle-fill fs-5 text-warning flex-shrink-0 mt-0.5"></i>
            <div>
                <strong class="d-block text-dark fw-bold mb-1" style="font-size: 14px;">Mode Akses Read-Only Admin (Data Di-sensor)</strong>
                <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.5;">
                    Data akun pengguna/user tidak dapat diubah oleh Admin. Informasi identitas seperti email dan username ditampilkan secara di-sensor demi privasi dan keamanan akun.
                </p>
            </div>
        </div>

        <div class="dt-card border-0 shadow-sm p-4" style="border-radius: 16px;">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 48px; height: 48px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">{{ $user->name }}</h5>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 11px;">
                            <i class="bi bi-eye-slash me-1"></i> Mode Baca
                        </span>
                    </div>
                </div>
                <span class="dt-badge {{ $user->status === 'aktif' ? 'dt-badge-success' : 'dt-badge-danger' }} fs-6">
                    {{ ucfirst($user->status) }}
                </span>
            </div>

            <div class="mb-3">
                <label class="dt-label text-muted fw-bold mb-1" style="font-size: 12px;">NAMA LENGKAP</label>
                <input type="text" class="form-control bg-light" value="{{ $user->name }}" readonly style="font-size: 13.5px;">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="dt-label text-muted fw-bold mb-1" style="font-size: 12px;">USERNAME (DI-SENSOR)</label>
                    <input type="text" class="form-control bg-light fw-semibold text-navy" value="{{ $user->masked_username }}" readonly style="font-size: 13.5px;">
                </div>
                <div class="col-md-6">
                    <label class="dt-label text-muted fw-bold mb-1" style="font-size: 12px;">EMAIL RESMI (DI-SENSOR)</label>
                    <input type="text" class="form-control bg-light fw-semibold text-navy" value="{{ $user->masked_email }}" readonly style="font-size: 13.5px;">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="dt-label text-muted fw-bold mb-1" style="font-size: 12px;">ROLE HAK AKSES</label>
                    <input type="text" class="form-control bg-light fw-bold text-dark" value="{{ ucfirst($user->role) }}" readonly style="font-size: 13.5px;">
                </div>
                <div class="col-md-6">
                    <label class="dt-label text-muted fw-bold mb-1" style="font-size: 12px;">TANGGAL TERDAFTAR</label>
                    <input type="text" class="form-control bg-light" value="{{ $user->created_at->format('d F Y H:i') }}" readonly style="font-size: 13.5px;">
                </div>
            </div>

            <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4 rounded-3" style="font-size: 13.5px;">
                    <i class="bi bi-arrow-left me-1.5"></i> Kembali ke Daftar Pengguna
                </a>
                <span class="text-muted small" style="font-size: 11.5px;">
                    <i class="bi bi-lock-fill text-warning me-1"></i> Data Dilindungi & Read-Only
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
