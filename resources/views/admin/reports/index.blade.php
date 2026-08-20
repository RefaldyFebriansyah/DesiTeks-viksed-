@extends('layouts.app')
@section('title', 'Laporan Lengkap')
@section('page-title', 'Laporan Toko DesiTeks')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Laporan Analisis & Ekspor</h1>
        <div class="dt-breadcrumb">Sistem / Laporan</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.export-csv', request()->all()) }}" class="dt-btn dt-btn-outline">
            <i class="bi bi-file-earmark-spreadsheet"></i> Ekspor CSV
        </a>
        <button onclick="window.print()" class="dt-btn dt-btn-primary">
            <i class="bi bi-printer"></i> Cetak Laporan
        </button>
    </div>
</div>

<div class="dt-card mb-4 no-print">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3">
            <label class="dt-label">Periode</label>
            <select name="periode" class="dt-select" onchange="this.form.submit()">
                <option value="hari_ini" {{ $periode == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                <option value="minggu_ini" {{ $periode == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="bulan_ini" {{ $periode == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="custom" {{ $periode == 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
            </select>
        </div>
        @if($periode == 'custom')
            <div class="col-sm-3">
                <label class="dt-label">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ request('dari') }}" class="dt-input">
            </div>
            <div class="col-sm-3">
                <label class="dt-label">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="dt-input">
            </div>
            <div class="col-auto">
                <button class="dt-btn dt-btn-primary"><i class="bi bi-search"></i> Terapkan</button>
            </div>
        @endif
    </form>
</div>

{{-- Stat Overview --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="dt-stat success">
            <div class="dt-stat-label">Total Pendapatan</div>
            <div class="dt-stat-value sm">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dt-stat gold">
            <div class="dt-stat-label">Total Transaksi</div>
            <div class="dt-stat-value">{{ $totalTransaksi }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dt-stat">
            <div class="dt-stat-label">Total Volume Terjual</div>
            <div class="dt-stat-value sm">{{ number_format($totalKainTerjual, 1) }} unit</div>
        </div>
    </div>
</div>

{{-- Tab Tables --}}
<div class="dt-card mb-4">
    <h5 class="fw-700 text-navy mb-3">Laporan Penjualan Per Kain ({{ $dari->format('d/m/Y') }} - {{ $sampai->format('d/m/Y') }})</h5>
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kain</th>
                    <th>Satuan Terjual</th>
                    <th>Total Volume</th>
                    <th>Total Omset (Rp)</th>
                </tr>
            </thead>
            <tbody>
            @forelse($penjualanPerKain as $item)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $item->fabric->kode_kain }}</span></td>
                    <td class="fw-600">{{ $item->fabric->nama_kain }}</td>
                    <td><span class="dt-badge dt-badge-gold">{{ ucfirst($item->satuan) }}</span></td>
                    <td>{{ number_format($item->total_jumlah, 1) }}</td>
                    <td class="fw-600 text-navy">Rp {{ number_format($item->total_subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada penjualan pada periode ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="dt-card">
    <h5 class="fw-700 text-navy mb-3">Laporan Ringkasan Stok Saat Ini</h5>
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kain</th>
                    <th>Kategori</th>
                    <th>Stok Rol</th>
                    <th>Stok Meter</th>
                    <th>Status Stok</th>
                </tr>
            </thead>
            <tbody>
            @foreach($stokReport as $stock)
                @php
                    $status = $stock->status;
                    $badgeClass = match($status) { 'habis'=>'dt-badge-danger','menipis'=>'dt-badge-warning', default=>'dt-badge-success' };
                @endphp
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $stock->fabric->kode_kain }}</span></td>
                    <td class="fw-600">{{ $stock->fabric->nama_kain }}</td>
                    <td>{{ $stock->fabric->category->nama_kategori }}</td>
                    <td>{{ $stock->stok_rol }} rol</td>
                    <td>{{ number_format($stock->stok_meter, 1) }} m</td>
                    <td><span class="dt-badge {{ $badgeClass }}">{{ ucfirst($status) }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
