@extends('layouts.app')
@section('title', 'Aktivitas Sistem')
@section('page-title', 'Aktivitas Sistem (Audit Log)')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Audit Log Aktivitas</h1>
        <div class="dt-breadcrumb">Sistem / Audit Log</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <!-- Unified Filter Toolbar -->
    <div class="p-3 bg-white border-bottom">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group min-w-0">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0 bg-light" placeholder="Cari aktivitas atau user..." style="font-size: 13.5px;" onchange="this.form.submit()">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="form-control bg-light" style="font-size: 13.5px;" onchange="this.form.submit()" title="Dari Tanggal">
            </div>
            <div class="col-6 col-md-3">
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="form-control bg-light" style="font-size: 13.5px;" onchange="this.form.submit()" title="Sampai Tanggal">
            </div>
            <div class="col-12 col-md-2 text-md-end ms-auto">
                @if(request()->hasAny(['search','tanggal_dari','tanggal_sampai']))
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-light border text-muted" title="Reset Filter">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="dt-table-wrap">
        <table class="dt-table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aktivitas</th>
                    <th>Model</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td><small class="text-muted">{{ $log->created_at->format('d/m/Y H:i:s') }}</small></td>
                    <td class="fw-600 text-navy">{{ $log->user->name ?? 'System' }}</td>
                    <td>{{ $log->aktivitas }}</td>
                    <td>@if($log->model)<span class="dt-badge dt-badge-navy">{{ $log->model }} #{{ $log->model_id }}</span>@else - @endif</td>
                    <td><small class="text-muted">{{ $log->ip_address ?? '-' }}</small></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada catatan aktivitas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top bg-light bg-opacity-30">{{ $logs->links() }}</div>
</div>
@endsection
