@extends('layouts.app')
@section('title', 'Riwayat Barang Masuk')
@section('page-title', 'Barang Masuk (Gudang)')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Barang Masuk</h1>
        <div class="dt-breadcrumb">Gudang / Barang Masuk</div>
    </div>
    <a href="{{ route('admin.incoming-goods.create') }}" class="dt-btn dt-btn-primary">
        <i class="bi bi-plus-lg"></i> Barang Masuk Baru
    </a>
</div>

<div class="dt-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-4">
            <input type="text" name="search" value="{{ request('search') }}" class="dt-input" placeholder="Cari no faktur / supplier...">
        </div>
        <div class="col-sm-3">
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="dt-input">
        </div>
        <div class="col-sm-3">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="dt-input">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="dt-btn dt-btn-primary"><i class="bi bi-search"></i></button>
            @if(request()->hasAny(['search','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.incoming-goods.index') }}" class="dt-btn dt-btn-outline"><i class="bi bi-x"></i></a>
            @endif
        </div>
    </form>
</div>

<div class="dt-card">
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>No Faktur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Petugas</th>
                    <th>Total Rol</th>
                    <th>Total Meter</th>
                    <th>Total Biaya</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($incomingGoods as $ig)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $ig->nomor_faktur }}</span></td>
                    <td>{{ $ig->tanggal->format('d/m/Y') }}</td>
                    <td class="fw-600 text-navy">{{ $ig->supplier->nama_supplier }}</td>
                    <td>{{ $ig->user->name }}</td>
                    <td>{{ $ig->total_rol }} rol</td>
                    <td>{{ number_format($ig->total_meter, 1) }} m</td>
                    <td class="fw-600 text-success">Rp {{ number_format($ig->total_pembelian, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('admin.incoming-goods.show', $ig) }}" class="dt-btn dt-btn-outline dt-btn-xs">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada barang masuk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $incomingGoods->links() }}</div>
</div>
@endsection
