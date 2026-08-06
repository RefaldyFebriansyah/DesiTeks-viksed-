@extends('layouts.app')
@section('title', '500 Kesalahan Server')
@section('page-title', '500 Server Error')

@section('content')
<div class="dt-card text-center py-5">
    <div style="font-size:72px;color:var(--dt-navy);line-height:1;">500</div>
    <h3 class="fw-700 text-navy mt-2">Terjadi Kesalahan Pada Server</h3>
    <p class="text-muted mb-4">Sistem mengalami gangguan internal. Silakan coba beberapa saat lagi.</p>
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
