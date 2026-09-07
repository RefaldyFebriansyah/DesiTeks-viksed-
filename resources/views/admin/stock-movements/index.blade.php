@extends('layouts.app')
@section('title', 'Riwayat Pergerakan Stok')
@section('page-title', 'Riwayat Pergerakan Stok')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Riwayat Pergerakan Stok</h1>
        <div class="dt-breadcrumb">Gudang / Riwayat Stok</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <!-- Unified Filter Toolbar -->
    <div class="p-3 bg-white border-bottom">
        <form method="GET" action="{{ route('admin.stock-movements.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-3">
                <div class="input-group min-w-0">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0 bg-light" placeholder="Cari nama kain..." style="font-size: 13.5px;" onchange="this.form.submit()">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <select name="jenis" class="form-select bg-light" style="font-size: 13.5px;" onchange="this.form.submit()">
                    <option value="">Semua Jenis Pergerakan</option>
                    <option value="barang_masuk" {{ request('jenis')=='barang_masuk'?'selected':'' }}>Barang Masuk</option>
                    <option value="penjualan" {{ request('jenis')=='penjualan'?'selected':'' }}>Penjualan</option>
                    <option value="penyesuaian" {{ request('jenis')=='penyesuaian'?'selected':'' }}>Penyesuaian Stok</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="form-control bg-light" style="font-size: 13.5px;" onchange="this.form.submit()" title="Dari Tanggal">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="form-control bg-light" style="font-size: 13.5px;" onchange="this.form.submit()" title="Sampai Tanggal">
            </div>
            <div class="col-12 col-md-2 text-md-end ms-auto">
                @if(request()->hasAny(['search','jenis','tanggal_dari','tanggal_sampai']))
                    <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-light border text-muted" title="Reset Filter">
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
                    <th>Nama Kain</th>
                    <th>Petugas</th>
                    <th>Jenis</th>
                    <th class="text-end">Perubahan Rol</th>
                    <th class="text-end">Perubahan Meter</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($movements as $m)
                @php
                    $ket = $m->keterangan;
                    if (str_contains($ket, 'Barang masuk ID:')) {
                        $id = preg_replace('/[^0-9]/', '', $ket);
                        $ket = "Penerimaan barang masuk (Gudang #{$id})";
                    } elseif (str_contains($ket, 'Sale ID:')) {
                        $ket = str_replace([' (Otomatis memotong/membuka ', ' utuh menjadi eceran)'], [' (Potong ', ' ke eceran)'], $ket);
                        $ket = preg_replace('/-\s*Sale ID:\s*\d+/', '', $ket);
                    }
                @endphp
                <tr>
                    <td>
                        <div class="fw-600 text-navy" style="font-size: 12.5px;">{{ $m->created_at->format('d/m/Y') }}</div>
                        <small class="text-muted">{{ $m->created_at->format('H:i') }} WIB</small>
                    </td>
                    <td class="fw-600 text-navy">{{ $m->fabric->nama_kain ?? '-' }}</td>
                    <td><span class="text-muted" style="font-size: 13px;">{{ $m->user->name ?? 'System' }}</span></td>
                    <td>
                        @if($m->jenis === 'barang_masuk')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-600">
                                Barang Masuk
                            </span>
                        @elseif($m->jenis === 'penjualan')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-600">
                                Penjualan
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2.5 py-1 fw-600">
                                Penyesuaian
                            </span>
                        @endif
                    </td>
                    <td class="fw-700 text-end">{{ $m->jumlah_rol }} rol</td>
                    <td class="fw-600 text-end text-muted">{{ number_format($m->jumlah_meter, 1) }} m</td>
                    <td>
                        <span class="text-secondary" style="font-size: 12.5px;">{{ $ket }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat pergerakan stok.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($movements, 'links'))
        <div class="p-3 border-top bg-light bg-opacity-30">{{ $movements->links() }}</div>
    @endif
</div>
@endsection
