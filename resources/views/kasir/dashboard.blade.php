@extends('layouts.app')
@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard Kasir')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="dt-stat success">
            <div class="dt-stat-icon"><i class="bi bi-cash-stack fs-5"></i></div>
            <div class="dt-stat-label">Pendapatan Hari Ini</div>
            <div class="dt-stat-value sm">Rp {{ number_format($pendapatanHariIni,0,',','.') }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-4">
        <div class="dt-stat gold">
            <div class="dt-stat-icon"><i class="bi bi-receipt fs-5"></i></div>
            <div class="dt-stat-label">Transaksi Hari Ini</div>
            <div class="dt-stat-value">{{ $transaksiHariIni }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-4">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-rulers fs-5"></i></div>
            <div class="dt-stat-label">Kain Terjual</div>
            <div class="dt-stat-value">{{ number_format($kainTerjualHariIni, 1) }} <small style="font-size:14px;font-weight:400">m/rol</small></div>
        </div>
    </div>
</div>

{{-- CTA --}}
<div class="mb-4">
    <a href="{{ route('kasir.sales.pos') }}" class="dt-btn dt-btn-gold" style="padding:14px 28px;font-size:16px">
        <i class="bi bi-plus-circle-fill"></i> Transaksi Baru
    </a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="dt-card">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-receipt me-2"></i>Transaksi Terbaru Hari Ini</span>
                <a href="{{ route('kasir.transactions.index') }}" class="dt-btn dt-btn-outline dt-btn-xs">Lihat Semua</a>
            </div>
            @if($transaksiTerbaru->isEmpty())
                <div class="empty-state"><i class="bi bi-inbox fs-3"></i><p class="mt-2">Belum ada transaksi hari ini</p>
                <a href="{{ route('kasir.sales.pos') }}" class="dt-btn dt-btn-primary dt-btn-sm mt-2">Mulai Transaksi</a></div>
            @else
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead><tr><th>No. Transaksi</th><th>Total</th><th>Bayar</th><th>Kembalian</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($transaksiTerbaru as $t)
                        <tr>
                            <td><a href="{{ route('kasir.transactions.show', $t) }}" class="fw-600 text-navy" style="text-decoration:none">{{ $t->nomor_transaksi }}</a>
                            <div style="font-size:11px;color:var(--dt-muted)">{{ $t->created_at->format('H:i') }}</div></td>
                            <td class="fw-600">Rp {{ number_format($t->total,0,',','.') }}</td>
                            <td>Rp {{ number_format($t->payment->jumlah_bayar ?? 0,0,',','.') }}</td>
                            <td>Rp {{ number_format($t->payment->kembalian ?? 0,0,',','.') }}</td>
                            <td><span class="dt-badge {{ $t->status==='berhasil' ? 'dt-badge-success' : 'dt-badge-danger' }}">{{ ucfirst($t->status) }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-5">
        <div class="dt-card">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-box-seam me-2"></i>Stok Tersedia</span>
                <a href="{{ route('kasir.stocks.index') }}" class="dt-btn dt-btn-outline dt-btn-xs">Lihat Semua</a>
            </div>
            @foreach($stokKain as $s)
            <div style="padding:9px 0;border-bottom:1px solid var(--dt-border);font-size:13px">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-600">{{ $s->fabric->nama_kain }}</div>
                        <div style="font-size:11px;color:var(--dt-muted)">{{ $s->fabric->kode_kain }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-600" style="font-size:12px">{{ number_format($s->stok_meter,1) }} m</div>
                        <div style="font-size:11px;color:var(--dt-muted)">{{ $s->stok_rol }} rol</div>
                    </div>
                </div>
            </div>
            @endforeach
            @if($stokKain->isEmpty())
                <div class="empty-state py-4"><i class="bi bi-inbox fs-3"></i><p class="mt-2">Tidak ada stok tersedia</p></div>
            @endif
        </div>
    </div>
</div>
@endsection
