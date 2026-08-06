@extends('layouts.app')
@section('title', 'Detail Barang Masuk')
@section('page-title', 'Detail Barang Masuk')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Faktur: {{ $incomingGood->nomor_faktur }}</h1>
        <div class="dt-breadcrumb">Gudang / Barang Masuk / Detail</div>
    </div>
    <a href="{{ route('admin.incoming-goods.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="dt-card">
            <h5 class="fw-700 text-navy mb-3">Informasi Barang Masuk</h5>
            <table class="table table-borderless table-sm fs-14">
                <tr><td class="text-muted">No. Faktur</td><td class="fw-600">{{ $incomingGood->nomor_faktur }}</td></tr>
                <tr><td class="text-muted">Supplier</td><td class="fw-600 text-navy">{{ $incomingGood->supplier->nama_supplier }}</td></tr>
                <tr><td class="text-muted">Tanggal Masuk</td><td>{{ $incomingGood->tanggal->format('d/m/Y') }}</td></tr>
                <tr><td class="text-muted">Dicatat Oleh</td><td>{{ $incomingGood->user->name }}</td></tr>
                <tr><td class="text-muted">Total Rol</td><td>{{ $incomingGood->total_rol }} rol</td></tr>
                <tr><td class="text-muted">Total Meter</td><td>{{ number_format($incomingGood->total_meter, 1) }} m</td></tr>
                <tr><td class="text-muted">Total Pembelian</td><td class="fw-700 text-success fs-16">Rp {{ number_format($incomingGood->total_pembelian, 0, ',', '.') }}</td></tr>
                <tr><td class="text-muted">Catatan</td><td>{{ $incomingGood->catatan ?? '-' }}</td></tr>
            </table>
        </div>
    </div>
    <div class="col-md-8">
        <div class="dt-card">
            <h5 class="fw-700 text-navy mb-3">Rincian Item Kain</h5>
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead>
                        <tr>
                            <th>Kain</th>
                            <th>Jml Rol</th>
                            <th>Jml Meter</th>
                            <th>Harga Beli/Meter</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($incomingGood->details as $d)
                        <tr>
                            <td>
                                <div class="fw-600">{{ $d->fabric->nama_kain }}</div>
                                <small class="text-muted">{{ $d->fabric->kode_kain }}</small>
                            </td>
                            <td>{{ $d->jumlah_rol }} rol</td>
                            <td>{{ number_format($d->jumlah_meter, 1) }} m</td>
                            <td>Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                            <td class="fw-600">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
