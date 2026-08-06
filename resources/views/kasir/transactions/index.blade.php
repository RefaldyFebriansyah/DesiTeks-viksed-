@extends('layouts.app')
@section('title', 'Transaksi Hari Ini')
@section('page-title', 'Transaksi Hari Ini')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Transaksi Penjualan Hari Ini</h1>
        <div class="dt-breadcrumb">Kasir / Transaksi Hari Ini</div>
    </div>
</div>

<div class="dt-card">
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>No Transaksi</th>
                    <th>Waktu</th>
                    <th>Metode</th>
                    <th>Total Belanja</th>
                    <th>Jumlah Bayar</th>
                    <th>Kembalian</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $t->nomor_transaksi }}</span></td>
                    <td>{{ $t->created_at->format('H:i') }} WIB</td>
                    <td><span class="dt-badge dt-badge-gold">{{ ucfirst($t->payment->metode ?? 'tunai') }}</span></td>
                    <td class="fw-600 text-navy">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($t->payment->jumlah_bayar ?? 0, 0, ',', '.') }}</td>
                    <td class="text-success fw-600">Rp {{ number_format($t->payment->kembalian ?? 0, 0, ',', '.') }}</td>
                    <td>
                        <span class="dt-badge {{ $t->status === 'berhasil' ? 'dt-badge-success' : 'dt-badge-danger' }}">
                            {{ ucfirst($t->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('kasir.transactions.show', $t) }}" class="dt-btn dt-btn-outline dt-btn-xs">Detail</a>
                            <a href="{{ route('kasir.sales.receipt', $t) }}" target="_blank" class="dt-btn dt-btn-gold dt-btn-xs"><i class="bi bi-printer"></i> Struk</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada transaksi hari ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $transactions->links() }}</div>
</div>
@endsection
