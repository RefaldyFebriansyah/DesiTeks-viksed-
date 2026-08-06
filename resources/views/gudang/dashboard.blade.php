@extends('layouts.app')
@section('title', 'Dashboard Gudang')
@section('page-title', 'Dashboard Gudang')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-grid-3x3-gap fs-5"></i></div>
            <div class="dt-stat-label">Total Jenis Kain</div>
            <div class="dt-stat-value">{{ number_format($totalJenisKain) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dt-stat gold">
            <div class="dt-stat-icon"><i class="bi bi-box-seam fs-5"></i></div>
            <div class="dt-stat-label">Total Stok Rol</div>
            <div class="dt-stat-value">{{ number_format($totalStokRol) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-rulers fs-5"></i></div>
            <div class="dt-stat-label">Total Stok Meter</div>
            <div class="dt-stat-value sm">{{ number_format($totalStokMeter, 1) }} m</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dt-stat success">
            <div class="dt-stat-icon"><i class="bi bi-arrow-down-circle fs-5"></i></div>
            <div class="dt-stat-label">Barang Masuk Hari Ini</div>
            <div class="dt-stat-value">{{ $barangMasukHariIni }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="dt-card">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-arrow-down-square me-2"></i>Barang Masuk Terbaru</span>
                <a href="{{ route('gudang.incoming-goods.create') }}" class="dt-btn dt-btn-primary dt-btn-sm">
                    <i class="bi bi-plus-lg"></i> Barang Masuk
                </a>
            </div>
            @if($barangMasukTerbaru->isEmpty())
                <div class="empty-state"><i class="bi bi-inbox fs-3"></i><p class="mt-2">Belum ada data barang masuk</p></div>
            @else
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead><tr><th>No. Faktur</th><th>Supplier</th><th>Tanggal</th><th>Total Rol</th><th>Total Meter</th><th>Total</th></tr></thead>
                    <tbody>
                    @foreach($barangMasukTerbaru as $bg)
                        <tr>
                            <td><a href="{{ route('gudang.incoming-goods.show', $bg) }}" class="fw-600 text-navy" style="text-decoration:none">{{ $bg->nomor_faktur }}</a></td>
                            <td>{{ $bg->supplier->nama_supplier }}</td>
                            <td>{{ $bg->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $bg->total_rol }} rol</td>
                            <td>{{ number_format($bg->total_meter,1) }} m</td>
                            <td class="fw-600">Rp {{ number_format($bg->total_pembelian,0,',','.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="dt-card">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-exclamation-triangle me-2" style="color:var(--dt-warning)"></i>Stok Menipis</span>
                <a href="{{ route('gudang.stocks.index') }}" class="dt-btn dt-btn-outline dt-btn-xs">Lihat Stok</a>
            </div>
            @if($stokMenipis->isEmpty())
                <div class="empty-state py-4"><i class="bi bi-check-circle text-success fs-3"></i><p class="mt-2 mb-0">Semua stok aman</p></div>
            @else
            @foreach($stokMenipis as $s)
            <div style="padding:10px 0;border-bottom:1px solid var(--dt-border);font-size:13px">
                <div class="fw-600">{{ $s->fabric->nama_kain }}</div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span style="color:var(--dt-muted);font-size:12px">{{ $s->fabric->kode_kain }}</span>
                    @if($s->stok_meter <= 0)
                        <span class="dt-badge dt-badge-danger">Habis</span>
                    @else
                        <span class="dt-badge dt-badge-warning">{{ number_format($s->stok_meter,1) }} m</span>
                    @endif
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>

@endsection
