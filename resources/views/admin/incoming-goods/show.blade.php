@extends('layouts.app')
@section('title', 'Detail Barang Masuk — {{ $incomingGood->nomor_faktur }}')
@section('page-title', 'Detail Barang Masuk')

@section('content')

{{-- Page Header --}}
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Detail Barang Masuk</h1>
        <div class="dt-breadcrumb">Admin / Barang Masuk / {{ $incomingGood->nomor_faktur }}</div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.delivery-orders.show', $incomingGood->getOrCreateDeliveryOrder()->id) }}" class="dt-btn dt-btn-primary dt-btn-sm d-inline-flex align-items-center gap-1.5">
            <i class="bi bi-eye"></i> Lihat Surat Jalan
        </a>
        <a href="{{ route('admin.delivery-orders.print', $incomingGood->getOrCreateDeliveryOrder()->id) }}" target="_blank" class="dt-btn dt-btn-outline dt-btn-sm d-inline-flex align-items-center gap-1.5">
            <i class="bi bi-file-earmark-pdf"></i> Unduh PDF Surat Jalan
        </a>
        @if($incomingGood->foto_lampiran)
        <a href="{{ asset('storage/' . $incomingGood->foto_lampiran) }}" target="_blank" download class="dt-btn dt-btn-outline dt-btn-sm d-inline-flex align-items-center gap-1.5">
            <i class="bi bi-image"></i> Unduh Foto Lampiran
        </a>
        @endif
    </div>
</div>

{{-- Dokumen Header --}}
<div class="dt-card mb-3 p-0 overflow-hidden">
    <div class="row g-0 border-bottom">
        {{-- Kiri: Identitas Dokumen --}}
        <div class="col-12 col-md-4 p-3 p-md-4 border-bottom border-md-bottom-0 border-md-end">
            <div class="text-muted text-uppercase fw-600 mb-1" style="font-size:10.5px; letter-spacing:0.8px;">No. Faktur</div>
            <div class="fw-700 text-navy fs-5 text-break">{{ $incomingGood->nomor_faktur }}</div>
            <div class="text-muted mt-2" style="font-size:12px;">
                <i class="bi bi-calendar3 me-1"></i>
                {{ $incomingGood->tanggal->format('d F Y') }}
            </div>
        </div>
        {{-- Tengah: Supplier --}}
        <div class="col-12 col-md-4 p-3 p-md-4 border-bottom border-md-bottom-0 border-md-end">
            <div class="text-muted text-uppercase fw-600 mb-1" style="font-size:10.5px; letter-spacing:0.8px;">Supplier</div>
            <div class="fw-600 text-navy fs-6">{{ $incomingGood->supplier->nama_supplier }}</div>
            <div class="mt-1">
                <span class="dt-badge dt-badge-navy" style="font-size:10.5px;">{{ $incomingGood->supplier->kode_supplier }}</span>
            </div>
            @if($incomingGood->supplier->no_telepon)
            <div class="text-muted mt-2" style="font-size:12px;">
                <i class="bi bi-telephone me-1"></i>{{ $incomingGood->supplier->no_telepon }}
            </div>
            @endif
        </div>
        {{-- Kanan: Dicatat Oleh --}}
        <div class="col-12 col-md-4 p-3 p-md-4">
            <div class="text-muted text-uppercase fw-600 mb-1" style="font-size:10.5px; letter-spacing:0.8px;">Dicatat Oleh</div>
            <div class="fw-600 text-navy fs-6">{{ $incomingGood->user->name }}</div>
            <div class="mt-1">
                <span class="dt-badge dt-badge-gold" style="font-size:10.5px;">{{ ucfirst($incomingGood->user->role) }}</span>
            </div>
            @if($incomingGood->catatan)
            <div class="text-muted mt-2 fst-italic" style="font-size:11.5px;">
                "{{ $incomingGood->catatan }}"
            </div>
            @endif
            <div class="mt-2 d-flex flex-column gap-1">
                <a href="{{ route('admin.delivery-orders.show', $incomingGood->getOrCreateDeliveryOrder()->id) }}" class="dt-btn dt-btn-gold dt-btn-xs w-100 w-md-auto d-inline-flex align-items-center justify-content-center gap-1">
                    <i class="bi bi-file-earmark-text"></i> Lihat Surat Jalan
                </a>
                @if($incomingGood->foto_lampiran)
                <button type="button" class="dt-btn dt-btn-outline dt-btn-xs w-100 w-md-auto d-inline-flex align-items-center justify-content-center gap-1 text-muted" data-bs-toggle="modal" data-bs-target="#suratJalanModalAdmin">
                    <i class="bi bi-image"></i> Lihat Foto Lampiran
                </button>
                @endif
            </div>
    </div>

    {{-- Ringkasan Angka --}}
    <div class="row g-0 bg-light">
        <div class="col-6 col-md-3 p-3 text-center border-end border-bottom border-md-bottom-0">
            <div class="text-muted text-uppercase fw-600 mb-1" style="font-size:10px; letter-spacing:0.5px;">Total Jenis Kain</div>
            <div class="fw-700 text-navy fs-4">{{ $incomingGood->details->count() > 0 ? $incomingGood->details->count() : '-' }}</div>
            <div class="text-muted" style="font-size:10.5px;">{{ $incomingGood->details->count() > 0 ? 'item' : 'ringkasan volume' }}</div>
        </div>
        <div class="col-6 col-md-3 p-3 text-center border-end-0 border-md-end border-bottom border-md-bottom-0">
            <div class="text-muted text-uppercase fw-600 mb-1" style="font-size:10px; letter-spacing:0.5px;">Total Rol</div>
            <div class="fw-700 text-navy fs-4">{{ $incomingGood->total_rol }}</div>
            <div class="text-muted" style="font-size:10.5px;">rol</div>
        </div>
        <div class="col-6 col-md-3 p-3 text-center border-end border-bottom-0">
            <div class="text-muted text-uppercase fw-600 mb-1" style="font-size:10px; letter-spacing:0.5px;">Total Meter</div>
            <div class="fw-700 text-navy fs-4">{{ number_format($incomingGood->total_meter, 1) }}</div>
            <div class="text-muted" style="font-size:10.5px;">meter</div>
        </div>
        <div class="col-6 col-md-3 p-3 text-center border-bottom-0">
            <div class="text-muted text-uppercase fw-600 mb-1" style="font-size:10px; letter-spacing:0.5px;">Total Pembelian</div>
            <div class="fw-700 text-success fs-6">Rp {{ number_format($incomingGood->total_pembelian, 0, ',', '.') }}</div>
            <div class="text-muted" style="font-size:10.5px;">nilai pembelian</div>
        </div>
    </div>
</div>

@if($incomingGood->details->count() > 0)
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
                    <th class="text-end">Harga Beli/m</th>
                    <th class="text-end">Subtotal</th>
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
                    <td class="text-end" style="font-size:13px;">Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-end fw-600" style="color:var(--dt-navy)">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8fafc; border-top: 2px solid var(--dt-border);">
                    <td colspan="4" style="font-size:13px; font-weight:600; color:var(--dt-muted); padding: 12px 16px;">TOTAL</td>
                    <td class="text-end fw-700" style="padding: 12px 16px;">{{ $incomingGood->total_rol }} <span style="font-size:11px;font-weight:400;color:var(--dt-muted)">rol</span></td>
                    <td class="text-end fw-700" style="padding: 12px 16px;">{{ number_format($incomingGood->total_meter, 1) }} <span style="font-size:11px;font-weight:400;color:var(--dt-muted)">m</span></td>
                    <td colspan="2" class="text-end fw-700 text-success" style="padding: 12px 16px;">Rp {{ number_format($incomingGood->total_pembelian, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@else
<div class="dt-card p-4 text-center">
    <div class="py-3">
        <i class="bi bi-box-seam text-secondary" style="font-size: 2.2rem;"></i>
        <div class="fw-600 mt-2" style="font-size: 14px; color:var(--dt-navy);">Penerimaan Barang Masuk Ringkasan Total</div>
        <div class="text-muted mt-1" style="font-size: 12.5px; max-width: 480px; margin: 0 auto;">
            Penerimaan ini dicatat dengan ringkasan volume total sebanyak <strong>{{ $incomingGood->total_rol }} Rol</strong> (<strong>{{ number_format($incomingGood->total_meter, 1) }} Meter</strong>) senilai <strong>Rp {{ number_format($incomingGood->total_pembelian, 0, ',', '.') }}</strong> tanpa rincian per-item kain.
        </div>
    </div>
</div>
@endif

{{-- Tombol Kembali di Bagian Bawah --}}
<div class="mt-4 mb-3 text-center">
    <a href="{{ route('admin.incoming-goods.index') }}" class="dt-btn dt-btn-outline px-4 py-2.5 shadow-sm d-inline-flex align-items-center justify-content-center gap-2" style="font-size:13.5px; font-weight:600; width:100%; max-width:320px;">
        <i class="bi bi-arrow-left fs-6"></i> Kembali ke Riwayat Barang Masuk
    </a>
</div>

@if($incomingGood->foto_lampiran)
<!-- Lightbox Modal Bukti Surat Jalan -->
<div class="modal fade" id="suratJalanModalAdmin" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="z-index: 1066; position: relative;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-navy text-white py-2.5 px-3">
                <h6 class="modal-title fw-700 m-0"><i class="bi bi-file-earmark-image me-1.5 text-gold"></i> Bukti Surat Jalan - {{ $incomingGood->nomor_faktur }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2 text-center bg-dark">
                <img src="{{ asset('storage/' . $incomingGood->foto_lampiran) }}" alt="Bukti Surat Jalan" class="img-fluid rounded" style="max-height: 80vh; width: auto; object-fit: contain;">
            </div>
            <div class="modal-footer py-2 px-3 justify-content-between bg-light">
                <span class="text-muted" style="font-size:12px;">Supplier: {{ $incomingGood->supplier->nama_supplier }}</span>
                <a href="{{ asset('storage/' . $incomingGood->foto_lampiran) }}" download class="dt-btn dt-btn-gold dt-btn-xs">
                    <i class="bi bi-download me-1"></i> Unduh File
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
