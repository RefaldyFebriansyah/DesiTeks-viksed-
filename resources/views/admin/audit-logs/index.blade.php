@extends('layouts.app')
@section('title', 'Aktivitas Sistem')
@section('page-title', 'Aktivitas Sistem (Audit Log)')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Audit Log Aktivitas</h1>
        <div class="dt-breadcrumb">Sistem / Audit Log</div>
    </div>
</div>

<div class="dt-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-4">
            <input type="text" name="search" value="{{ request('search') }}" class="dt-input" placeholder="Cari aktivitas atau user...">
        </div>
        <div class="col-sm-3">
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="dt-input">
        </div>
        <div class="col-sm-3">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="dt-input">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="dt-btn dt-btn-primary"><i class="bi bi-search"></i></button>
            @if(request()->hasAny(['search','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.audit-logs.index') }}" class="dt-btn dt-btn-outline"><i class="bi bi-x"></i></a>
            @endif
        </div>
    </form>
</div>

<div class="dt-card">
    <div class="dt-table-wrap">
        <table class="dt-table">
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
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada catatan aktivitas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $logs->links() }}</div>
</div>
@endsection
