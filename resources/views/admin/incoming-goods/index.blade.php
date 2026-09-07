@extends('layouts.app')
@section('title', 'Riwayat Barang Masuk')
@section('page-title', 'Barang Masuk')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Riwayat Barang Masuk</h1>
        <div class="dt-breadcrumb">Gudang & Supplier / Riwayat Barang Masuk</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <!-- Combined Toolbar: Integrated Search, Date Filters & Action Button -->
    <div class="p-3 bg-white border-bottom">
        <form method="GET" action="{{ route('admin.incoming-goods.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group min-w-0">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="searchInput" autocomplete="off" class="form-control border-start-0 ps-0 bg-light" placeholder="Ketik no faktur atau supplier..." value="{{ request('search') }}" style="font-size: 13.5px;">
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
                    <a href="{{ route('admin.incoming-goods.index') }}" class="btn btn-light border text-muted" title="Reset Filter">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                @endif
                <a href="{{ route('admin.incoming-goods.create') }}" class="dt-btn dt-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="dt-table-wrap">
        <table class="dt-table mb-0">
            <thead>
                <tr>
                    <th>No Faktur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Petugas</th>
                    <th class="text-end">Total Rol</th>
                    <th class="text-end">Total Meter</th>
                    <th class="text-end">Total Biaya</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($incomingGoods as $ig)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $ig->nomor_faktur }}</span></td>
                    <td>{{ $ig->tanggal->format('d/m/Y') }}</td>
                    <td class="fw-600 text-navy">{{ $ig->supplier->nama_supplier }}</td>
                    <td>{{ $ig->user->name }}</td>
                    <td class="text-end fw-600">{{ $ig->total_rol }} rol</td>
                    <td class="text-end fw-600 text-muted">{{ number_format($ig->total_meter, 1) }} m</td>
                    <td class="text-end fw-600 text-success">Rp {{ number_format($ig->total_pembelian, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('admin.incoming-goods.show', $ig) }}">
                                    <i class="bi bi-eye me-1.5"></i> Detail Faktur
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada riwayat barang masuk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top bg-light bg-opacity-40 d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <div class="text-muted" style="font-size: 12.5px;">
            Menampilkan <strong>{{ $incomingGoods->firstItem() ?? 0 }}</strong> - <strong>{{ $incomingGoods->lastItem() ?? 0 }}</strong> dari total <strong>{{ $incomingGoods->total() }}</strong> barang masuk (maks. 10 per halaman)
        </div>
        <div class="ms-md-auto">
            {{ $incomingGoods->links() }}
        </div>
    </div>
</div>
@endsection
