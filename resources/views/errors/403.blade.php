@extends('layouts.app')
@section('title', '403 Akses Ditolak')
@section('page-title', '403 Forbidden')

@section('content')
<div class="dt-card text-center py-5">
    <div style="font-size:72px;color:var(--dt-danger);line-height:1;">403</div>
    <h3 class="fw-700 text-navy mt-2">Akses Ditolak</h3>
    <p class="text-muted mb-4">Anda tidak memiliki hak akses untuk membuka halaman ini.</p>
    <div>
        @php
            $role = auth()->user()->role ?? '';
            $route = match($role) {
                'admin' => route('admin.dashboard'),
                'gudang' => route('gudang.dashboard'),
                'kasir' => route('kasir.dashboard'),
                default => route('login'),
            };
        @endphp
        <a href="{{ $route }}" class="dt-btn dt-btn-primary">
            <i class="bi bi-house me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
