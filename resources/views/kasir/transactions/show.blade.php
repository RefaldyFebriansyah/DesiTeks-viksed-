@extends('layouts.app')
@section('title', 'Detail Transaksi — ' . $sale->nomor_transaksi)
@section('page-title', 'Detail Transaksi Kasir')

@section('content')

{{-- Page Header --}}
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Detail Transaksi</h1>
        <div class="dt-breadcrumb">Kasir / Transaksi Hari Ini / {{ $sale->nomor_transaksi }}</div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="dt-btn dt-btn-gold" onclick="printReceiptDirect('{{ route('kasir.sales.receipt', $sale) }}')">
            <i class="bi bi-printer"></i> Cetak Struk
        </button>
        <a href="{{ route('kasir.transactions.index') }}" class="dt-btn dt-btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- Dokumen Header --}}
<div class="dt-card mb-3" style="padding: 0; overflow: hidden;">
    <div style="display:flex; align-items:stretch; border-bottom: 1px solid var(--dt-border);">
        {{-- Kiri: Identitas Transaksi --}}
        <div style="flex:1; padding: 20px 24px; border-right: 1px solid var(--dt-border);">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:var(--dt-muted); margin-bottom:6px;">No. Transaksi</div>
            <div style="font-size:20px; font-weight:700; color:var(--dt-navy); letter-spacing:.3px;">{{ $sale->nomor_transaksi }}</div>
            <div style="margin-top:10px; font-size:13px; color:var(--dt-muted);">
                <i class="bi bi-calendar3 me-1"></i>
                {{ $sale->created_at->format('d F Y H:i') }} WIB
            </div>
        </div>
        {{-- Tengah: Info Pembayaran --}}
        <div style="flex:1; padding: 20px 24px; border-right: 1px solid var(--dt-border);">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:var(--dt-muted); margin-bottom:6px;">Metode Pembayaran</div>
            <div style="font-size:15px; font-weight:600; color:var(--dt-navy);">
                {{ strtolower($sale->payment->metode ?? '') === 'qris' ? 'QRIS' : ucfirst($sale->payment->metode ?? 'Tunai') }}
            </div>
            <div style="margin-top:8px; font-size:13px; color:var(--dt-muted);">
                Status: 
                <span class="dt-badge {{ $sale->status === 'berhasil' ? 'dt-badge-success' : 'dt-badge-danger' }}">
                    {{ ucfirst($sale->status) }}
                </span>
            </div>
        </div>
        {{-- Kanan: Kasir --}}
        <div style="flex:1; padding: 20px 24px;">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:var(--dt-muted); margin-bottom:6px;">Kasir / Operator</div>
            <div style="font-size:15px; font-weight:600; color:var(--dt-navy);">{{ $sale->user->name }}</div>
            <div style="margin-top:4px; font-size:12px; color:var(--dt-muted);">
                ID Kasir: <span class="dt-badge dt-badge-navy">#{{ $sale->user->id }}</span>
            </div>
        </div>
    </div>

    {{-- Ringkasan angka pembayaran --}}
    <div style="display:flex; background: #f8fafc;">
        <div style="flex:1; padding:14px 24px; text-align:center; border-right:1px solid var(--dt-border);">
            <div style="font-size:11px; color:var(--dt-muted); font-weight:600; text-transform:uppercase; letter-spacing:.8px;">Total Belanja</div>
            <div style="font-size:22px; font-weight:700; color:var(--dt-navy); margin-top:2px;">Rp {{ number_format($sale->total, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; padding:14px 24px; text-align:center; border-right:1px solid var(--dt-border);">
            <div style="font-size:11px; color:var(--dt-muted); font-weight:600; text-transform:uppercase; letter-spacing:.8px;">Jumlah Bayar</div>
            <div style="font-size:22px; font-weight:700; color:var(--dt-navy); margin-top:2px;">Rp {{ number_format($sale->payment->jumlah_bayar ?? 0, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; padding:14px 24px; text-align:center;">
            <div style="font-size:11px; color:var(--dt-muted); font-weight:600; text-transform:uppercase; letter-spacing:.8px;">Kembalian</div>
            <div style="font-size:22px; font-weight:700; color:var(--dt-success); margin-top:2px;">Rp {{ number_format($sale->payment->kembalian ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

{{-- Tabel Rincian Item --}}
<div class="dt-card">
    <div style="padding: 16px 20px 12px; border-bottom: 1px solid var(--dt-border); display:flex; align-items:center; justify-content:space-between;">
        <div>
            <div style="font-size:14px; font-weight:600; color:var(--dt-navy);">Item Pembelian</div>
            <div style="font-size:12px; color:var(--dt-muted); margin-top:1px;">{{ $sale->details->count() }} jenis produk dalam transaksi ini</div>
        </div>
    </div>
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th>Kode Kain</th>
                    <th>Nama Kain</th>
                    <th>Satuan</th>
                    <th class="text-end">Jumlah</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            @foreach($sale->details as $i => $detail)
                <tr>
                    <td style="color:var(--dt-muted); font-size:12px;">{{ $i + 1 }}</td>
                    <td><span class="dt-badge dt-badge-navy">{{ $detail->fabric->kode_kain }}</span></td>
                    <td>
                        <div class="fw-600" style="color:var(--dt-navy)">{{ $detail->fabric->nama_kain }}</div>
                        <div style="font-size:11px; color:var(--dt-muted)">{{ $detail->fabric->jenis_kain }}</div>
                    </td>
                    <td>
                        <span class="dt-badge {{ $detail->satuan === 'meter' ? 'dt-badge-gold' : 'dt-badge-navy' }}">
                            {{ ucfirst($detail->satuan) }}
                        </span>
                    </td>
                    <td class="text-end fw-600">
                        {{ fmod((float)$detail->jumlah, 1) == 0 ? number_format($detail->jumlah, 0, ',', '.') : rtrim(rtrim(number_format($detail->jumlah, 2, ',', '.'), '0'), ',') }} 
                        <span style="font-size:11px;font-weight:400;color:var(--dt-muted)">{{ $detail->satuan }}</span>
                    </td>
                    <td class="text-end" style="font-size:13px;">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-end fw-600" style="color:var(--dt-navy)">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8fafc; border-top: 2px solid var(--dt-border);">
                    <td colspan="4" style="font-size:13px; font-weight:600; color:var(--dt-muted); padding: 12px 16px;">TOTAL AKHIR</td>
                    <td colspan="2"></td>
                    <td class="text-end fw-700" style="font-size:16px; color:var(--dt-navy); padding: 12px 16px;">
                        Rp {{ number_format($sale->total, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
