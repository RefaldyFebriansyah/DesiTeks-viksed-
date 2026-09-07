@extends('layouts.app')
@section('title', 'Barang Masuk Gudang')
@section('page-title', 'Barang Masuk (Gudang)')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Barang Masuk</h1>
        <div class="dt-breadcrumb">Gudang / Barang Masuk</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <!-- Unified Toolbar Card: Combined Search Input & Tambah Barang Masuk Button -->
    <div class="p-3 bg-white border-bottom">
        <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-3">
            <form method="GET" action="{{ route('gudang.incoming-goods.index') }}" class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 520px;">
                <div class="input-group min-w-0">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="searchInput" autocomplete="off" class="form-control border-start-0 ps-0 bg-light" placeholder="Ketik no faktur / supplier..." value="{{ request('search') }}" style="font-size: 13.5px;">
                    @if(request('search'))
                        <a href="{{ route('gudang.incoming-goods.index') }}" class="btn btn-light border border-start-0 text-muted" title="Reset"><i class="bi bi-x-circle-fill"></i></a>
                    @endif
                </div>
            </form>

            <a href="{{ route('gudang.incoming-goods.create') }}" class="dt-btn dt-btn-primary flex-shrink-0 ms-md-auto">
                <i class="bi bi-plus-lg me-1"></i> Tambah Barang Masuk
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="dt-table-wrap">
        <table class="dt-table mb-0">
            <thead>
                <tr>
                    <th>No Faktur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th class="text-end">Total Rol</th>
                    <th class="text-end">Total Meter</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($incomingGoods as $ig)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $ig->nomor_faktur }}</span></td>
                    <td>{{ $ig->tanggal->format('d/m/Y') }}</td>
                    <td class="fw-600 text-navy">{{ $ig->supplier->nama_supplier }}</td>
                    <td class="text-end fw-600">{{ $ig->total_rol }} rol</td>
                    <td class="text-end fw-600 text-muted">{{ number_format($ig->total_meter, 1) }} m</td>
                    <td class="text-center">
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('gudang.incoming-goods.show', $ig) }}">
                                    <i class="bi bi-eye me-1.5"></i> Detail Faktur
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data barang masuk.</td></tr>
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
