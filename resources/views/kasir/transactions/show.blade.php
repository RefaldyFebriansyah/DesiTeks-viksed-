@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi Kasir')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Transaksi: {{ $sale->nomor_transaksi }}</h1>
        <div class="dt-breadcrumb">Kasir / Transaksi Hari Ini / Detail</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('kasir.sales.receipt', $sale) }}" target="_blank" class="dt-btn dt-btn-gold">
            <i class="bi bi-printer"></i> Struk
        </a>
        <a href="{{ route('kasir.transactions.index') }}" class="dt-btn dt-btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="dt-card">
            <h5 class="fw-700 text-navy mb-3">Rincian Pembayaran</h5>
            <table class="table table-borderless table-sm fs-14">
                <tr><td class="text-muted">No. Transaksi</td><td class="fw-600">{{ $sale->nomor_transaksi }}</td></tr>
                <tr><td class="text-muted">Waktu Transaksi</td><td>{{ $sale->created_at->format('d/m/Y H:i') }}</td></tr>
                <tr><td class="text-muted">Kasir</td><td>{{ $sale->user->name }}</td></tr>
                <tr><td class="text-muted">Status</td><td><span class="dt-badge {{ $sale->status === 'berhasil' ? 'dt-badge-success' : 'dt-badge-danger' }}">{{ ucfirst($sale->status) }}</span></td></tr>
                <tr><td class="text-muted">Metode Bayar</td><td class="fw-600">{{ ucfirst($sale->payment->metode ?? 'tunai') }}</td></tr>
                <tr><td class="text-muted">Total Belanja</td><td class="fw-700 text-navy fs-16">Rp {{ number_format($sale->total, 0, ',', '.') }}</td></tr>
                <tr><td class="text-muted">Jumlah Bayar</td><td>Rp {{ number_format($sale->payment->jumlah_bayar ?? 0, 0, ',', '.') }}</td></tr>
                <tr><td class="text-muted">Kembalian</td><td class="fw-600 text-success">Rp {{ number_format($sale->payment->kembalian ?? 0, 0, ',', '.') }}</td></tr>
            </table>
        </div>
    </div>
    <div class="col-md-8">
        <div class="dt-card">
            <h5 class="fw-700 text-navy mb-3">Item Pembelian</h5>
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead>
                        <tr>
                            <th>Kain</th>
                            <th>Satuan</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($sale->details as $detail)
                        <tr>
                            <td>
                                <div class="fw-600">{{ $detail->fabric->nama_kain }}</div>
                                <small class="text-muted">{{ $detail->fabric->kode_kain }}</small>
                            </td>
                            <td><span class="dt-badge dt-badge-navy">{{ ucfirst($detail->satuan) }}</span></td>
                            <td>{{ number_format($detail->jumlah, $detail->satuan === 'meter' ? 1 : 0) }}</td>
                            <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td class="fw-600">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
