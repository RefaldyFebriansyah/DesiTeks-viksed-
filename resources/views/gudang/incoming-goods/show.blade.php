@extends('layouts.app')
@section('title', 'Detail Barang Masuk — {{ $incomingGood->nomor_faktur }}')
@section('page-title', 'Detail Barang Masuk')

@section('content')

{{-- Page Header --}}
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Detail Barang Masuk</h1>
        <div class="dt-breadcrumb">Gudang / Barang Masuk / {{ $incomingGood->nomor_faktur }}</div>
    </div>
    <a href="{{ route('gudang.incoming-goods.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

{{-- Dokumen Header --}}
<div class="dt-card mb-3" style="padding: 0; overflow: hidden;">
    <div style="display:flex; align-items:stretch; border-bottom: 1px solid var(--dt-border);">
        {{-- Kiri: identitas dokumen --}}
        <div style="flex:1; padding: 20px 24px; border-right: 1px solid var(--dt-border);">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:var(--dt-muted); margin-bottom:6px;">No. Faktur</div>
            <div style="font-size:20px; font-weight:700; color:var(--dt-navy); letter-spacing:.3px;">{{ $incomingGood->nomor_faktur }}</div>
            <div style="margin-top:10px; font-size:13px; color:var(--dt-muted);">
                <i class="bi bi-calendar3 me-1"></i>
                {{ $incomingGood->tanggal->format('d F Y') }}
            </div>
        </div>
        {{-- Tengah: supplier --}}
        <div style="flex:1; padding: 20px 24px; border-right: 1px solid var(--dt-border);">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:var(--dt-muted); margin-bottom:6px;">Supplier</div>
            <div style="font-size:15px; font-weight:600; color:var(--dt-navy);">{{ $incomingGood->supplier->nama_supplier }}</div>
            <div style="margin-top:4px; font-size:12px; color:var(--dt-muted);">
                <span class="dt-badge dt-badge-navy">{{ $incomingGood->supplier->kode_supplier }}</span>
            </div>
            @if($incomingGood->supplier->no_telepon)
            <div style="margin-top:8px; font-size:13px; color:var(--dt-muted);">
                <i class="bi bi-telephone me-1"></i>{{ $incomingGood->supplier->no_telepon }}
            </div>
            @endif
        </div>
        {{-- Kanan: dicatat oleh --}}
        <div style="flex:1; padding: 20px 24px;">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:var(--dt-muted); margin-bottom:6px;">Dicatat Oleh</div>
            <div style="font-size:15px; font-weight:600; color:var(--dt-navy);">{{ $incomingGood->user->name }}</div>
            <div style="margin-top:4px; font-size:12px; color:var(--dt-muted);">
                <span class="dt-badge dt-badge-gold">{{ ucfirst($incomingGood->user->role) }}</span>
            </div>
            @if($incomingGood->catatan)
            <div style="margin-top:8px; font-size:12px; color:var(--dt-muted); font-style:italic;">
                "{{ $incomingGood->catatan }}"
            </div>
            @endif
            @if($incomingGood->foto_lampiran)
            <div style="margin-top:10px;">
                <a href="{{ asset('storage/' . $incomingGood->foto_lampiran) }}" target="_blank" class="dt-btn dt-btn-gold dt-btn-xs" style="padding: 4px 10px; font-size: 11px;">
                    <i class="bi bi-image"></i> Lihat Foto Nota
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Ringkasan angka --}}
    <div style="display:flex; background: #f8fafc;">
        <div style="flex:1; padding:14px 24px; text-align:center; border-right:1px solid var(--dt-border);">
            <div style="font-size:11px; color:var(--dt-muted); font-weight:600; text-transform:uppercase; letter-spacing:.8px;">Total Jenis Kain</div>
            <div style="font-size:22px; font-weight:700; color:var(--dt-navy); margin-top:2px;">{{ $incomingGood->details->count() }}</div>
            <div style="font-size:11px; color:var(--dt-muted);">item</div>
        </div>
        <div style="flex:1; padding:14px 24px; text-align:center; border-right:1px solid var(--dt-border);">
            <div style="font-size:11px; color:var(--dt-muted); font-weight:600; text-transform:uppercase; letter-spacing:.8px;">Total Rol</div>
            <div style="font-size:22px; font-weight:700; color:var(--dt-navy); margin-top:2px;">{{ $incomingGood->total_rol }}</div>
            <div style="font-size:11px; color:var(--dt-muted);">rol</div>
        </div>
        <div style="flex:1; padding:14px 24px; text-align:center;">
            <div style="font-size:11px; color:var(--dt-muted); font-weight:600; text-transform:uppercase; letter-spacing:.8px;">Total Meter</div>
            <div style="font-size:22px; font-weight:700; color:var(--dt-navy); margin-top:2px;">{{ number_format($incomingGood->total_meter, 1) }}</div>
            <div style="font-size:11px; color:var(--dt-muted);">meter</div>
        </div>
    </div>
</div>

{{-- Tabel Rincian Item --}}
<div class="dt-card">
    <div style="padding: 16px 20px 12px; border-bottom: 1px solid var(--dt-border); display:flex; align-items:center; justify-content:space-between;">
        <div>
            <div style="font-size:14px; font-weight:600; color:var(--dt-navy);">Rincian Item Kain</div>
            <div style="font-size:12px; color:var(--dt-muted); margin-top:1px;">{{ $incomingGood->details->count() }} jenis kain dalam penerimaan ini</div>
        </div>
    </div>
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th>Kode Kain</th>
                    <th>Nama Kain</th>
                    <th>Kategori</th>
                    <th class="text-end">Jumlah Rol</th>
                    <th class="text-end">Jumlah Meter</th>
                </tr>
            </thead>
            <tbody>
            @foreach($incomingGood->details as $i => $d)
                <tr>
                    <td style="color:var(--dt-muted); font-size:12px;">{{ $i + 1 }}</td>
                    <td><span class="dt-badge dt-badge-navy">{{ $d->fabric->kode_kain }}</span></td>
                    <td>
                        <div class="fw-600" style="color:var(--dt-navy)">{{ $d->fabric->nama_kain }}</div>
                        <div style="font-size:11px; color:var(--dt-muted)">{{ $d->fabric->jenis_kain }}</div>
                    </td>
                    <td style="font-size:13px; color:var(--dt-muted)">{{ $d->fabric->category->nama_kategori ?? '-' }}</td>
                    <td class="text-end fw-600">{{ $d->jumlah_rol }} <span style="font-size:11px;font-weight:400;color:var(--dt-muted)">rol</span></td>
                    <td class="text-end fw-600">{{ number_format($d->jumlah_meter, 1) }} <span style="font-size:11px;font-weight:400;color:var(--dt-muted)">m</span></td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8fafc; border-top: 2px solid var(--dt-border);">
                    <td colspan="4" style="font-size:13px; font-weight:600; color:var(--dt-muted); padding: 12px 16px;">TOTAL</td>
                    <td class="text-end fw-700" style="padding: 12px 16px;">{{ $incomingGood->total_rol }} <span style="font-size:11px;font-weight:400;color:var(--dt-muted)">rol</span></td>
                    <td class="text-end fw-700" style="padding: 12px 16px;">{{ number_format($incomingGood->total_meter, 1) }} <span style="font-size:11px;font-weight:400;color:var(--dt-muted)">m</span></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
