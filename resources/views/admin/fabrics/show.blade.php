@extends('layouts.app')
@section('title', 'Detail Kain — ' . $fabric->nama_kain)
@section('page-title', 'Detail Data Kain')

@section('content')

{{-- Page Header --}}
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Spesifikasi Kain</h1>
        <div class="dt-breadcrumb">Master Data / Kain / {{ $fabric->kode_kain }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fabrics.edit', $fabric) }}" class="dt-btn dt-btn-primary">
            <i class="bi bi-pencil"></i> Edit Data
        </a>
        <a href="{{ route('admin.fabrics.index') }}" class="dt-btn dt-btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- Dokumen Header --}}
<div class="dt-card mb-3" style="padding: 0; overflow: hidden;">
    <div style="display:flex; align-items:stretch; border-bottom: 1px solid var(--dt-border);">
        {{-- Kiri: Kode & Nama --}}
        <div style="flex:1.2; padding: 20px 24px; border-right: 1px solid var(--dt-border);">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:var(--dt-muted); margin-bottom:6px;">Kode & Jenis Kain</div>
            <div style="font-size:20px; font-weight:700; color:var(--dt-navy); letter-spacing:.3px;">{{ $fabric->kode_kain }}</div>
            <div style="font-size:16px; font-weight:600; color:var(--dt-navy); margin-top:4px;">{{ $fabric->nama_kain }}</div>
            <div style="margin-top:10px; font-size:13px; color:var(--dt-muted);">
                Kategori: <span class="dt-badge dt-badge-navy">{{ $fabric->category->nama_kategori }}</span>
            </div>
        </div>
        {{-- Tengah: Fisik Kain --}}
        <div style="flex:1; padding: 20px 24px; border-right: 1px solid var(--dt-border);">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:var(--dt-muted); margin-bottom:6px;">Spesifikasi Fisik</div>
            <table class="table table-borderless table-sm m-0 fs-13" style="color: var(--dt-text);">
                <tr><td class="p-0 pb-1 text-muted" style="width:70px;">Bahan</td><td class="p-0 pb-1 fw-600">: {{ $fabric->jenis_kain ?? '-' }}</td></tr>
                <tr><td class="p-0 pb-1 text-muted">Warna</td><td class="p-0 pb-1 fw-600">: {{ $fabric->warna ?? '-' }}</td></tr>
                <tr><td class="p-0 pb-1 text-muted">Motif</td><td class="p-0 pb-1 fw-600">: {{ $fabric->motif ?? '-' }}</td></tr>
            </table>
        </div>
        {{-- Kanan: Harga & Status --}}
        <div style="flex:1; padding: 20px 24px;">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:var(--dt-muted); margin-bottom:6px;">Harga Acuan</div>
            <table class="table table-borderless table-sm m-0 fs-13" style="color: var(--dt-text);">
                <tr><td class="p-0 pb-1 text-muted" style="width:90px;">Harga/Meter</td><td class="p-0 pb-1 fw-600 text-navy">: Rp {{ number_format($fabric->harga_per_meter, 0, ',', '.') }}</td></tr>
                <tr><td class="p-0 pb-1 text-muted">Harga/Rol</td><td class="p-0 pb-1 fw-600 text-navy">: Rp {{ number_format($fabric->harga_per_rol, 0, ',', '.') }}</td></tr>
                <tr>
                    <td class="p-0 text-muted">Status</td>
                    <td class="p-0">: 
                        <span class="dt-badge {{ $fabric->status === 'aktif' ? 'dt-badge-success' : 'dt-badge-danger' }}">
                            {{ ucfirst($fabric->status) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Ringkasan angka stok --}}
    <div style="display:flex; background: #f8fafc;">
        <div style="flex:1; padding:14px 24px; text-align:center; border-right:1px solid var(--dt-border);">
            <div style="font-size:11px; color:var(--dt-muted); font-weight:600; text-transform:uppercase; letter-spacing:.8px;">Stok Terkini (Rol)</div>
            <div style="font-size:22px; font-weight:700; color:var(--dt-navy); margin-top:2px;">{{ $fabric->stock->stok_rol ?? 0 }} <span style="font-size:13px; font-weight:400; color:var(--dt-muted);">rol</span></div>
        </div>
        <div style="flex:1; padding:14px 24px; text-align:center; border-right:1px solid var(--dt-border);">
            <div style="font-size:11px; color:var(--dt-muted); font-weight:600; text-transform:uppercase; letter-spacing:.8px;">Stok Terkini (Meter)</div>
            <div style="font-size:22px; font-weight:700; color:var(--dt-navy); margin-top:2px;">{{ number_format($fabric->stock->stok_meter ?? 0, 1) }} <span style="font-size:13px; font-weight:400; color:var(--dt-muted);">m</span></div>
        </div>
        <div style="flex:1; padding:14px 24px; text-align:center;">
            <div style="font-size:11px; color:var(--dt-muted); font-weight:600; text-transform:uppercase; letter-spacing:.8px;">Kondisi Stok</div>
            @php
                $statusStok = $fabric->status_stok;
                $badgeClass = match($statusStok) { 
                    'habis'=>'dt-badge-danger',
                    'menipis'=>'dt-badge-warning', 
                    default=>'dt-badge-success' 
                };
            @endphp
            <div style="margin-top:6px;">
                <span class="dt-badge {{ $badgeClass }}" style="font-size:12px; padding:6px 14px;">
                    {{ strtoupper($statusStok) }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Pergerakan Stok --}}
<div class="dt-card">
    <div style="padding: 16px 20px 12px; border-bottom: 1px solid var(--dt-border); display:flex; align-items:center; justify-content:space-between;">
        <div>
            <div style="font-size:14px; font-weight:600; color:var(--dt-navy);">Riwayat Pergerakan Stok (Mutasi)</div>
            <div style="font-size:12px; color:var(--dt-muted); margin-top:1px;">Log perputaran stok masuk dan keluar untuk kain ini</div>
        </div>
    </div>
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>Waktu Mutasi</th>
                    <th>User Pelaksana</th>
                    <th>Tipe Perubahan</th>
                    <th class="text-end">Mutasi Rol</th>
                    <th class="text-end">Mutasi Meter</th>
                    <th>Keterangan / Referensi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($fabric->stockMovements as $move)
                <tr>
                    <td style="font-size:13px; color:var(--dt-muted)">{{ $move->created_at->format('d/m/Y H:i') }} WIB</td>
                    <td>
                        <div class="fw-600" style="color:var(--dt-navy)">{{ $move->user->name ?? 'Sistem' }}</div>
                        <div style="font-size:11px; color:var(--dt-muted)">{{ ucfirst($move->user->role ?? 'otomatis') }}</div>
                    </td>
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
                    <td class="text-end fw-600 {{ $move->jumlah_rol < 0 ? 'text-danger' : 'text-success' }}">
                        {{ $move->jumlah_rol > 0 ? '+' : '' }}{{ $move->jumlah_rol }}
                    </td>
                    <td class="text-end fw-600 {{ $move->jumlah_meter < 0 ? 'text-danger' : 'text-success' }}">
                        {{ $move->jumlah_meter > 0 ? '+' : '' }}{{ number_format($move->jumlah_meter, 1) }} m
                    </td>
                    <td style="font-size:13px; color:var(--dt-muted)">{{ $move->keterangan }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat pergerakan stok (mutasi).</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
