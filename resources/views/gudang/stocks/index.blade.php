@extends('layouts.app')
@section('title', 'Stok Kain Gudang')
@section('page-title', 'Data Stok Kain')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Stok Kain</h1>
        <div class="dt-breadcrumb">Gudang / Stok</div>
    </div>
</div>

<div class="dt-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-5">
            <input type="text" name="search" value="{{ request('search') }}" class="dt-input" placeholder="Cari kode atau nama kain...">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="dt-btn dt-btn-primary"><i class="bi bi-search"></i> Cari</button>
        </div>
    </form>
</div>

<div class="dt-card">
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kain</th>
                    <th>Kategori</th>
                    <th>Stok Rol</th>
                    <th>Stok Meter</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($stocks as $stock)
                @php
                    $status = $stock->status;
                    $badgeClass = match($status) { 'habis'=>'dt-badge-danger','menipis'=>'dt-badge-warning', default=>'dt-badge-success' };
                @endphp
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $stock->fabric->kode_kain }}</span></td>
                    <td class="fw-600 text-navy">{{ $stock->fabric->nama_kain }}</td>
                    <td>{{ $stock->fabric->category->nama_kategori }}</td>
                    <td class="fw-600">{{ $stock->stok_rol }} rol</td>
                    <td class="fw-600">{{ number_format($stock->stok_meter, 1) }} m</td>
                    <td><span class="dt-badge {{ $badgeClass }}">{{ ucfirst($status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data stok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $stocks->links() }}</div>
</div>
@endsection
