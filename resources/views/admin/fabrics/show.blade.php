@extends('layouts.app')
@section('title', 'Detail Kain')
@section('page-title', 'Detail Kain')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Detail Kain: {{ $fabric->nama_kain }}</h1>
        <div class="dt-breadcrumb">Master Data / Kain / Detail</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fabrics.edit', $fabric) }}" class="dt-btn dt-btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit Kain
        </a>
        <a href="{{ route('admin.fabrics.index') }}" class="dt-btn dt-btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="dt-card">
            <h5 class="fw-700 text-navy mb-3">Informasi Kain</h5>
            <table class="table table-borderless table-sm fs-14">
                <tr><td class="text-muted">Kode Kain</td><td class="fw-600">{{ $fabric->kode_kain }}</td></tr>
                <tr><td class="text-muted">Nama Kain</td><td class="fw-600">{{ $fabric->nama_kain }}</td></tr>
                <tr><td class="text-muted">Kategori</td><td>{{ $fabric->category->nama_kategori }}</td></tr>
                <tr><td class="text-muted">Jenis</td><td>{{ $fabric->jenis_kain ?? '-' }}</td></tr>
                <tr><td class="text-muted">Warna</td><td>{{ $fabric->warna ?? '-' }}</td></tr>
                <tr><td class="text-muted">Motif</td><td>{{ $fabric->motif ?? '-' }}</td></tr>
                <tr><td class="text-muted">Harga/Meter</td><td class="fw-600 text-navy">Rp {{ number_format($fabric->harga_per_meter, 0, ',', '.') }}</td></tr>
                <tr><td class="text-muted">Harga/Rol</td><td class="fw-600 text-navy">Rp {{ number_format($fabric->harga_per_rol, 0, ',', '.') }}</td></tr>
                <tr><td class="text-muted">Status Akun</td><td><span class="dt-badge {{ $fabric->status == 'aktif' ? 'dt-badge-success' : 'dt-badge-danger' }}">{{ ucfirst($fabric->status) }}</span></td></tr>
            </table>
        </div>
        <div class="dt-card mt-3">
            <h5 class="fw-700 text-navy mb-3">Stok Saat Ini</h5>
            <div class="d-flex justify-content-around text-center py-2">
                <div>
                    <span class="text-muted d-block fs-12">STOK ROL</span>
                    <span class="fw-700 fs-24 text-navy">{{ $fabric->stock->stok_rol ?? 0 }}</span>
                </div>
                <div style="border-left: 1px solid var(--dt-border)"></div>
                <div>
                    <span class="text-muted d-block fs-12">STOK METER</span>
                    <span class="fw-700 fs-24 text-navy">{{ number_format($fabric->stock->stok_meter ?? 0, 1) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="dt-card">
            <h5 class="fw-700 text-navy mb-3">Riwayat Pergerakan Stok Kain</h5>
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Jenis</th>
                            <th>Jumlah Rol</th>
                            <th>Jumlah Meter</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($fabric->stockMovements as $move)
                        <tr>
                            <td>{{ $move->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $move->user->name ?? 'Sistem' }}</td>
                            <td>
                                @php
                                    $badge = match($move->jenis) {
                                        'barang_masuk' => 'dt-badge-success',
                                        'penjualan' => 'dt-badge-danger',
                                        default => 'dt-badge-warning',
                                    };
                                @endphp
                                <span class="dt-badge {{ $badge }}">{{ str_replace('_', ' ', ucfirst($move->jenis)) }}</span>
                            </td>
                            <td>{{ $move->jumlah_rol }}</td>
                            <td>{{ number_format($move->jumlah_meter, 1) }}</td>
                            <td><small>{{ $move->keterangan }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat pergerakan stok.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
