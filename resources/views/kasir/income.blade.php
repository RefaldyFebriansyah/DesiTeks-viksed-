@extends('layouts.app')
@section('title', 'Pendapatan Hari Ini')
@section('page-title', 'Ringkasan Pendapatan Hari Ini')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Pendapatan Hari Ini</h1>
        <div class="dt-breadcrumb">Kasir / Pendapatan Hari Ini</div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="dt-stat success">
            <div class="dt-stat-label">Total Pendapatan (Hari Ini)</div>
            <div class="dt-stat-value sm">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dt-stat gold">
            <div class="dt-stat-label">Total Transaksi Selesai</div>
            <div class="dt-stat-value">{{ $transaksiHariIni }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dt-stat">
            <div class="dt-stat-label">Total Volume Kain Terjual</div>
            <div class="dt-stat-value sm">{{ number_format($kainTerjualHariIni, 1) }} unit</div>
        </div>
    </div>
</div>

<div class="dt-card">
    <h5 class="fw-700 text-navy mb-3">Rincian Transaksi Selesai Hari Ini</h5>
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>No Transaksi</th>
                    <th>Waktu</th>
                    <th>Metode Pembayaran</th>
                    <th>Total Belanja</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transaksi as $t)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $t->nomor_transaksi }}</span></td>
                    <td>{{ $t->created_at->format('H:i') }} WIB</td>
                    <td><span class="dt-badge dt-badge-gold">{{ ucfirst($t->payment->metode ?? 'tunai') }}</span></td>
                    <td class="fw-600 text-navy">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td><span class="dt-badge dt-badge-success">Berhasil</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi berhasil hari ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
