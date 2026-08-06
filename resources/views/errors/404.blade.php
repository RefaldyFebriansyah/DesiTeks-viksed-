@extends('layouts.app')
@section('title', '404 Halaman Tidak Ditemukan')
@section('page-title', '404 Not Found')

@section('content')
<div class="dt-card text-center py-5">
    <div style="font-size:72px;color:var(--dt-gold);line-height:1;">404</div>
    <h3 class="fw-700 text-navy mt-2">Halaman Tidak Ditemukan</h3>
    <p class="text-muted mb-4">Halaman yang Anda cari mungkin telah dihapus atau URL tidak valid.</p>
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
