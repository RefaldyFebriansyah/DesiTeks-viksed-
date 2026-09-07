@extends('layouts.app')
@section('title', 'Laporan Lengkap')
@section('page-title', 'Laporan Toko DesiTeks')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Laporan Analisis & Ekspor</h1>
        <div class="dt-breadcrumb">Sistem / Laporan</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.export-csv', request()->all()) }}" class="dt-btn dt-btn-outline">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Ekspor CSV
        </a>
        <button onclick="window.print()" class="dt-btn dt-btn-primary">
            <i class="bi bi-printer me-1"></i> Cetak Laporan
        </button>
    </div>
</div>

<div class="dt-card mb-4 no-print border-0 shadow-sm" style="border-radius: 14px;">
    <div class="p-3 bg-white" style="border-radius: 14px;">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-3">
                <label class="dt-label text-muted mb-1" style="font-size: 11px;">Periode Laporan</label>
                <select name="periode" class="form-select bg-light" style="font-size: 13.5px;" onchange="this.form.submit()">
                    <option value="hari_ini" {{ $periode == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu_ini" {{ $periode == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan_ini" {{ $periode == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="custom" {{ $periode == 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
                </select>
            </div>
            @if($periode == 'custom')
                <div class="col-6 col-md-3">
                    <label class="dt-label text-muted mb-1" style="font-size: 11px;">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" class="form-control bg-light" style="font-size: 13.5px;" onchange="this.form.submit()">
                </div>
                <div class="col-6 col-md-3">
                    <label class="dt-label text-muted mb-1" style="font-size: 11px;">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control bg-light" style="font-size: 13.5px;" onchange="this.form.submit()">
                </div>
            @endif
        </form>
    </div>
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
<div class="dt-card mb-4 border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <div class="p-3 bg-white border-bottom fw-700 text-navy" style="font-size: 14px;">
        <i class="bi bi-graph-up-arrow me-1.5 text-primary"></i> Laporan Penjualan Per Kain ({{ $dari->format('d/m/Y') }} - {{ $sampai->format('d/m/Y') }})
    </div>
    <div class="dt-table-wrap">
        <table class="dt-table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kain</th>
                    <th>Satuan Terjual</th>
                    <th class="text-end">Total Volume</th>
                    <th class="text-end">Total Omset</th>
                </tr>
            </thead>
            <tbody>
            @forelse($penjualanPerKain as $item)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $item->fabric->kode_kain }}</span></td>
                    <td class="fw-600 text-navy">{{ $item->fabric->nama_kain }}</td>
                    <td><span class="dt-badge dt-badge-gold">{{ ucfirst($item->satuan) }}</span></td>
                    <td class="text-end fw-600">{{ number_format($item->total_jumlah, 1) }}</td>
                    <td class="text-end fw-600 text-navy">Rp {{ number_format($item->total_subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada penjualan pada periode ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <div class="p-3 bg-white border-bottom fw-700 text-navy" style="font-size: 14px;">
        <i class="bi bi-box-seam me-1.5 text-primary"></i> Ringkasan Stok Kain Saat Ini
    </div>
    <div class="dt-table-wrap">
        <table class="dt-table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kain</th>
                    <th>Kategori</th>
                    <th class="text-end">Stok Rol</th>
                    <th class="text-end">Stok Meter</th>
                    <th class="text-center">Status Stok</th>
                </tr>
            </thead>
            <tbody>
            @foreach($stokReport as $stock)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $stock->fabric->kode_kain }}</span></td>
                    <td class="fw-600 text-navy">{{ $stock->fabric->nama_kain }}</td>
                    <td>{{ $stock->fabric->category->nama_kategori }}</td>
                    <td class="text-end fw-700 text-navy">{{ $stock->stok_rol }} rol</td>
                    <td class="text-end fw-600 text-muted">{{ number_format($stock->stok_meter, 1) }} m</td>
                    <td class="text-center" style="white-space: nowrap;">
                        @if($stock->stok_rol <= 0 && $stock->stok_meter <= 0)
                            <span class="badge-stok-habis"><i class="bi bi-x-circle-fill me-1"></i> Habis</span>
                        @elseif($stock->stok_rol <= 5)
                            <span class="badge-stok-menipis"><i class="bi bi-exclamation-triangle-fill me-1"></i> Stok Menipis</span>
                        @else
                            <span class="badge-stok-aman"><i class="bi bi-check-circle-fill me-1"></i> Stok Aman</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
