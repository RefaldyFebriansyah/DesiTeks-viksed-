@extends('layouts.app')
@section('title', 'Riwayat Pergerakan Stok')
@section('page-title', 'Riwayat Pergerakan Stok')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Riwayat Movement Stok</h1>
        <div class="dt-breadcrumb">Gudang / Riwayat Stok</div>
    </div>
</div>

<div class="dt-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3">
            <input type="text" name="search" value="{{ request('search') }}" class="dt-input" placeholder="Cari nama kain...">
        </div>
        <div class="col-sm-3">
            <select name="jenis" class="dt-select">
                <option value="">Semua Jenis Pergerakan</option>
                <option value="barang_masuk" {{ request('jenis')=='barang_masuk'?'selected':'' }}>Barang Masuk</option>
                <option value="penjualan" {{ request('jenis')=='penjualan'?'selected':'' }}>Penjualan</option>
                <option value="penyesuaian" {{ request('jenis')=='penyesuaian'?'selected':'' }}>Penyesuaian (Adjustment)</option>
            </select>
        </div>
        <div class="col-sm-2">
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="dt-input">
        </div>
        <div class="col-sm-2">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="dt-input">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="dt-btn dt-btn-primary"><i class="bi bi-search"></i></button>
            @if(request()->hasAny(['search','jenis','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.stock-movements.index') }}" class="dt-btn dt-btn-outline"><i class="bi bi-x"></i></a>
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
                    <th>Nama Kain</th>
                    <th>Petugas</th>
                    <th>Jenis</th>
                    <th>Perubahan Rol</th>
                    <th>Perubahan Meter</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($movements as $m)
                <tr>
                    <td><small class="text-muted">{{ $m->created_at->format('d/m/Y H:i') }}</small></td>
                    <td class="fw-600 text-navy">{{ $m->fabric->nama_kain }}</td>
                    <td>{{ $m->user->name ?? 'System' }}</td>
                    <td>
                        @php
                            $badge = match($m->jenis) {
                                'barang_masuk' => 'dt-badge-success',
                                'penjualan' => 'dt-badge-danger',
                                default => 'dt-badge-warning',
                            };
                        @endphp
                        <span class="dt-badge {{ $badge }}">{{ str_replace('_', ' ', ucfirst($m->jenis)) }}</span>
                    </td>
                    <td>{{ $m->jumlah_rol }} rol</td>
                    <td>{{ number_format($m->jumlah_meter, 1) }} m</td>
                    <td><small>{{ $m->keterangan }}</small></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat pergerakan stok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $movements->links() }}</div>
</div>
@endsection
