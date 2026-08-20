@extends('layouts.app')
@section('title', 'Transaksi Berhasil')
@section('page-title', 'Transaksi Berhasil')

@section('content')
@php $role = auth()->user()->role; @endphp

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="dt-card text-center py-5 px-4 shadow-lg border-0" style="border-radius: 16px; overflow: hidden; position: relative;">
            {{-- Success Icon with Animated Ring --}}
            <div class="success-icon-wrap mb-4">
                <div class="success-icon-ring"></div>
                <div class="success-icon bg-success-soft text-success rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-check-lg" style="font-size: 38px;"></i>
                </div>
            </div>

            <h3 class="fw-800 text-navy mb-1" style="letter-spacing: -0.5px;">Transaksi Berhasil!</h3>
            <p class="text-muted mb-4" style="font-size: 14.5px;">Pembayaran telah diterima dan invoice berhasil dicatat.</p>

            {{-- Invoice Detail Table --}}
            <div class="dt-card mb-4 border border-light" style="background:#f8fafc; text-align:left; border-radius: 12px; padding: 18px 20px;">
                <h5 class="fw-700 text-navy mb-3" style="font-size: 14px; border-bottom: 1px solid var(--dt-border); padding-bottom: 8px;">
                    <i class="bi bi-file-earmark-text-fill text-gold me-1.5"></i> Ringkasan Invoice
                </h5>
                <table style="width:100%; font-size:13.5px; border-collapse: collapse;">
                    <tr>
                        <td style="padding:8px 0; color:var(--dt-muted)">No. Transaksi</td>
                        <td class="fw-700 text-navy" style="text-align:right">{{ $sale->nomor_transaksi }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:var(--dt-muted)">Tanggal & Waktu</td>
                        <td style="text-align:right; color:var(--dt-text)">{{ $sale->created_at->format('d M Y, H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:var(--dt-muted)">Kasir</td>
                        <td style="text-align:right; color:var(--dt-text)">{{ $sale->user->name }}</td>
                    </tr>
                    @if($sale->customer)
                    <tr>
                        <td style="padding:8px 0; color:var(--dt-muted)">Pelanggan</td>
                        <td style="text-align:right;" class="fw-600 text-navy">{{ $sale->customer->nama }} <span class="badge bg-navy text-white ms-1 rounded-pill" style="font-size: 10px; font-weight: 500;">{{ ucfirst($sale->customer->tipe) }}</span></td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding:8px 0; color:var(--dt-muted)">Total Belanja</td>
                        <td class="fw-700 text-navy" style="text-align:right; font-size:15px;">Rp {{ number_format($sale->total,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:var(--dt-muted)">Uang Diterima</td>
                        <td style="text-align:right; color:var(--dt-text)">Rp {{ number_format($sale->payment->jumlah_bayar,0,',','.') }}</td>
                    </tr>
                    <tr style="border-top:1.5px dashed var(--dt-border)">
                        <td style="padding:12px 0 0 0; font-weight:700; color: var(--dt-text)">Kembalian</td>
                        <td class="fw-800 text-success" style="text-align:right; font-size:20px; padding:12px 0 0 0;">Rp {{ number_format($sale->payment->kembalian,0,',','.') }}</td>
                    </tr>
                </table>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                @php 
                    $receiptRoute = $role === 'admin' ? route('admin.sales.receipt', $sale) : route('kasir.sales.receipt', $sale);
                    $newPosRoute = $role === 'admin' ? route('admin.sales.pos') : route('kasir.sales.pos');
                @endphp
                <a href="{{ $receiptRoute }}" target="_blank" class="dt-btn dt-btn-outline py-2.5 px-4 justify-content-center text-navy" style="border-radius: 8px; font-weight: 600;">
                    <i class="bi bi-printer-fill me-2"></i> Cetak Struk Belanja
                </a>
                <a href="{{ $newPosRoute }}" class="dt-btn dt-btn-gold py-2.5 px-4 justify-content-center" style="border-radius: 8px; font-weight: 600;">
                    <i class="bi bi-plus-lg me-2"></i> Buat Transaksi Baru
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Success Icon Ring Animation */
.success-icon-wrap {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto;
}
.success-icon {
    width: 80px;
    height: 80px;
    position: relative;
    z-index: 2;
    border: 2px solid var(--dt-success);
}
.success-icon-ring {
    position: absolute;
    top: 0;
    left: 0;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid rgba(46, 204, 113, 0.3);
    animation: iconRingPulse 2s infinite ease-out;
    z-index: 1;
}
@keyframes iconRingPulse {
    0% { transform: scale(1); opacity: 1; }
    100% { transform: scale(1.4); opacity: 0; }
}
</style>

{{-- Hidden iframe untuk otomatisasi cetak struk thermal instan --}}
<iframe id="receiptIframe" src="{{ $receiptRoute }}" style="display:none; width:0; height:0; border:0;"></iframe>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('receiptIframe');
    if (iframe) {
        iframe.onload = function() {
            // Tunggu sebentar agar render style CSS di dalam iframe selesai sempurna
            setTimeout(() => {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch (e) {
                    console.error("Gagal melakukan auto-print:", e);
                }
            }, 600);
        };
    }
});
</script>
@endsection
