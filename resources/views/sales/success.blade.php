@extends('layouts.app')
@section('title', 'Transaksi Berhasil')
@section('page-title', 'Transaksi Berhasil')

@section('content')
@php $role = auth()->user()->role; @endphp

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="dt-card text-center py-4">
            <div style="width:72px;height:72px;background:var(--dt-success-bg);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                <i class="bi bi-check-circle-fill" style="font-size:36px;color:var(--dt-success)"></i>
            </div>
            <h4 class="fw-700 text-navy mb-1">Transaksi Berhasil!</h4>
            <p class="text-muted mb-4" style="font-size:14px">Pembayaran telah diterima.</p>

            <div class="dt-card mb-4" style="background:var(--dt-bg);text-align:left">
                <table style="width:100%;font-size:14px">
                    <tr><td style="padding:6px 0;color:var(--dt-muted)">No. Transaksi</td><td class="fw-600 text-navy" style="text-align:right">{{ $sale->nomor_transaksi }}</td></tr>
                    <tr><td style="padding:6px 0;color:var(--dt-muted)">Tanggal</td><td style="text-align:right">{{ $sale->created_at->format('d/m/Y H:i') }}</td></tr>
                    <tr><td style="padding:6px 0;color:var(--dt-muted)">Kasir</td><td style="text-align:right">{{ $sale->user->name }}</td></tr>
                    <tr><td style="padding:6px 0;color:var(--dt-muted)">Total Belanja</td><td class="fw-700" style="text-align:right;font-size:16px;color:var(--dt-navy)">Rp {{ number_format($sale->total,0,',','.') }}</td></tr>
                    <tr><td style="padding:6px 0;color:var(--dt-muted)">Pembayaran</td><td style="text-align:right">Rp {{ number_format($sale->payment->jumlah_bayar,0,',','.') }}</td></tr>
                    <tr style="border-top:2px solid var(--dt-border)">
                        <td style="padding:8px 0;font-weight:600">Kembalian</td>
                        <td class="fw-700" style="text-align:right;font-size:18px;color:var(--dt-success)">Rp {{ number_format($sale->payment->kembalian,0,',','.') }}</td>
                    </tr>
                </table>
            </div>

            <div class="d-flex gap-3 justify-content-center">
                @if($role === 'admin')
                    <a href="{{ route('admin.sales.receipt', $sale) }}" target="_blank" class="dt-btn dt-btn-outline">
                        <i class="bi bi-printer me-1"></i> Cetak Struk
                    </a>
                    <a href="{{ route('admin.sales.pos') }}" class="dt-btn dt-btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Transaksi Baru
                    </a>
                @else
                    <a href="{{ route('kasir.sales.receipt', $sale) }}" target="_blank" class="dt-btn dt-btn-outline">
                        <i class="bi bi-printer me-1"></i> Cetak Struk
                    </a>
                    <a href="{{ route('kasir.sales.pos') }}" class="dt-btn dt-btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Transaksi Baru
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
