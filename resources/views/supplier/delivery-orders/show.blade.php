@extends('layouts.supplier')

@section('title', 'Surat Jalan ' . $deliveryOrder->nomor_surat_jalan)

@push('styles')
<style>
    .surat-jalan-paper {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        color: #0f172a;
        font-size: 13.5px;
    }
    .sj-header-title {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: 0.05em;
        color: #0f172a;
        text-transform: uppercase;
    }
    .sj-box {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        background-color: #ffffff;
    }
    .sj-table {
        width: 100%;
        border-collapse: collapse;
    }
    .sj-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
    }
    .sj-table td {
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        color: #1e293b;
    }
    .sj-table tfoot td {
        background-color: #f8fafc;
        font-weight: 700;
        border: 1px solid #e2e8f0;
    }
    .signature-line {
        border-bottom: 1.5px solid #0f172a;
        width: 75%;
        margin: 45px auto 6px auto;
    }

    /* Status Pill with Dot Indicator */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 12.5px;
        font-weight: 600;
        line-height: 1.3;
        white-space: nowrap;
    }
    .status-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-warning { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    .status-warning .dot { background: #d97706; }

    .status-info    { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
    .status-info .dot    { background: #2563eb; }

    .status-success { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
    .status-success .dot { background: #16a34a; }

    .status-danger  { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
    .status-danger .dot  { background: #dc2626; }
</style>
@endpush

@section('content')
<div class="pb-5">
    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('supplier.delivery-orders.index') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center mb-1.5 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Surat Jalan
            </a>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">{{ $deliveryOrder->nomor_surat_jalan }}</h3>
                @if($deliveryOrder->status === 'menunggu_approval')
                    <span class="status-pill status-warning"><span class="dot"></span> Menunggu ACC Admin Toko</span>
                @elseif($deliveryOrder->status === 'disetujui_admin' || $deliveryOrder->status === 'dikirim')
                    <span class="status-pill status-info"><span class="dot"></span> Disetujui Admin — Siap Kirim</span>
                @elseif($deliveryOrder->status === 'dalam_perjalanan')
                    <span class="status-pill status-info" style="background: #e0f2fe; color: #0284c7; border-color: #bae6fd;"><span class="dot" style="background: #0284c7;"></span> Dalam Perjalanan</span>
                @elseif($deliveryOrder->status === 'diterima')
                    <span class="status-pill status-success"><span class="dot"></span> Diterima Gudang</span>
                @elseif($deliveryOrder->status === 'ditolak')
                    <span class="status-pill status-danger"><span class="dot"></span> Ditolak</span>
                @else
                    <span class="status-pill status-info"><span class="dot"></span> {{ ucfirst(str_replace('_', ' ', $deliveryOrder->status)) }}</span>
                @endif
            </div>
        </div>

        <!-- Direct Print & Action Buttons -->
        <div class="d-flex gap-2">
            @if(in_array($deliveryOrder->status, ['disetujui_admin', 'dikirim']))
                <button type="button" class="btn btn-primary fw-bold px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKirimInfo" style="font-size: 13px; border-radius: 8px; background: #2563eb; border: none;">
                    <i class="bi bi-send-fill"></i>
                    <span>Kirim Info: Barang Dalam Perjalanan</span>
                </button>
            @endif

            <button type="button" onclick="directPrintSuratJalan('{{ route('supplier.delivery-orders.print', $deliveryOrder->id) }}')" class="btn btn-outline-secondary px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 13px; border-radius: 8px;">
                <i class="bi bi-printer"></i>
                <span>Cetak Surat Jalan</span>
            </button>
        </div>
    </div>

    <!-- Status Summary Card (Clean Neutral) -->
    @if($deliveryOrder->status === 'menunggu_approval')
        <div class="p-3 mb-4 rounded-3 bg-white border d-flex align-items-center gap-3">
            <i class="bi bi-info-circle text-secondary fs-5 flex-shrink-0"></i>
            <div>
                <strong class="text-dark d-block" style="font-size: 13.5px;">Surat Jalan Menunggu ACC Admin Toko</strong>
                <span class="small text-muted">
                    Dokumen pengiriman telah diterbitkan dan sedang menunggu persetujuan Admin Toko DesiTeks.
                </span>
            </div>
        </div>
    @elseif(in_array($deliveryOrder->status, ['disetujui_admin', 'dikirim', 'dalam_perjalanan']))
        <div class="p-3 mb-4 rounded-3 bg-white border d-flex align-items-center gap-3">
            <i class="bi bi-info-circle text-secondary fs-5 flex-shrink-0"></i>
            <div>
                <strong class="text-dark d-block" style="font-size: 13.5px;">
                    {{ $deliveryOrder->status === 'dalam_perjalanan' ? 'Barang Sedang Dalam Perjalanan Ke Gudang' : 'Surat Jalan Di-ACC Admin Toko' }}
                </strong>
                <span class="small text-muted">
                    Disetujui oleh <strong>{{ $deliveryOrder->approvedByAdmin?->name ?? 'Admin Toko' }}</strong>. 
                    @if($deliveryOrder->status === 'dalam_perjalanan')
                        Informasi keberangkatan telah diterima gudang.
                    @else
                        Silakan konfirmasi keberangkatan jika armada telah berangkat.
                    @endif
                </span>
            </div>
        </div>
    @elseif($deliveryOrder->status === 'diterima')
        <div class="p-3 mb-4 rounded-3 bg-white border d-flex align-items-center gap-3">
            <i class="bi bi-check-circle text-secondary fs-5 flex-shrink-0"></i>
            <div>
                <strong class="text-dark d-block" style="font-size: 13.5px;">Pengiriman Telah Diterima Gudang</strong>
                <span class="small text-muted">
                    Diverifikasi oleh <strong>{{ $deliveryOrder->receivedBy?->name ?? 'Staf Gudang' }}</strong> pada {{ $deliveryOrder->received_at?->format('d F Y, H:i') }} WIB.
                </span>
                @if($deliveryOrder->catatan_gudang)
                    <div class="small text-dark mt-1.5 p-2 bg-light rounded border">
                        <strong>Catatan Gudang:</strong> {{ $deliveryOrder->catatan_gudang }}
                    </div>
                @endif
            </div>
        </div>
    @elseif($deliveryOrder->status === 'ditolak')
        <div class="p-3 mb-4 rounded-3 bg-white border d-flex align-items-center gap-3">
            <i class="bi bi-x-circle text-secondary fs-5 flex-shrink-0"></i>
            <div>
                <strong class="text-dark d-block" style="font-size: 13.5px;">Pengiriman Ditolak</strong>
                <span class="small text-muted">
                    Ditolak pada {{ $deliveryOrder->received_at?->format('d F Y, H:i') ?? $deliveryOrder->updated_at->format('d F Y, H:i') }} WIB.
                </span>
                @if($deliveryOrder->catatan_gudang)
                    <div class="small text-dark mt-1.5 p-2 bg-light rounded border">
                        <strong>Alasan Penolakan:</strong> {{ $deliveryOrder->catatan_gudang }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Stepper Progress Tracker -->
    @include('partials.delivery-order-stepper')

    <!-- Lembar Surat Jalan Resmi -->
    <div class="surat-jalan-paper p-4 p-md-5 mb-4">
        <!-- Kop Surat Jalan (Identitas Perusahaan Supplier Pengirim) -->
        <div class="row align-items-center pb-4 mb-4 border-bottom border-2 border-dark">
            <div class="col-sm-7">
                <div class="fs-4 fw-bold text-dark text-uppercase mb-1" style="letter-spacing: -0.01em;">{{ $deliveryOrder->supplier->nama_supplier }}</div>
                <div class="fw-bold text-secondary small text-uppercase mb-1">Mitra Supplier Kain & Tekstil</div>
                <div class="text-muted" style="font-size: 11.5px;">
                    Kode Rekanan: <strong>{{ $deliveryOrder->supplier->kode_supplier }}</strong>
                    @if($deliveryOrder->supplier->asal_kota) • Kota: {{ $deliveryOrder->supplier->asal_kota }} @endif
                    @if($deliveryOrder->supplier->no_telepon) • Telp: +62{{ $deliveryOrder->supplier->no_telepon }} @endif
                    @if($deliveryOrder->supplier->email) • Email: {{ $deliveryOrder->supplier->email }} @endif
                </div>
                @if($deliveryOrder->supplier->alamat)
                    <div class="text-muted" style="font-size: 11.5px;">Alamat: {{ $deliveryOrder->supplier->alamat }}</div>
                @endif
            </div>
            <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                <div class="sj-header-title">SURAT JALAN</div>
                <div class="fw-bold text-dark fs-6 mt-1">No: {{ $deliveryOrder->nomor_surat_jalan }}</div>
                <div class="text-secondary small">Tanggal Kirim: <strong>{{ $deliveryOrder->tanggal_kirim->format('d F Y') }}</strong></div>
            </div>
        </div>

        <!-- Informasi Penerima (Tujuan DesiTeks) & Ekspedisi -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="sj-box h-100">
                    <div class="text-uppercase fw-bold small text-muted border-bottom pb-1 mb-2">KEPADA YTH. (PENERIMA):</div>
                    <div class="fw-bold text-dark fs-6">PT. DESITEKS SEJAHTERA NUSA</div>
                    <div class="text-primary fw-bold small">{{ $deliveryOrder->branch?->nama_cabang ?? 'Gudang Utama DesiTeks' }}</div>
                    <div class="text-secondary small mt-1">{{ $deliveryOrder->branch?->alamat ?? 'Jl. Kebon Jati No. 45, Bandung, Jawa Barat' }}</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="sj-box h-100">
                    <div class="text-uppercase fw-bold small text-muted border-bottom pb-1 mb-2">PENGANGKUTAN & EKSPEDISI:</div>
                    <div class="small">
                        <div><strong>Ekspedisi / Armada:</strong> {{ $deliveryOrder->ekspedisi ?: 'Pengiriman Mandiri / Truk Box' }}</div>
                        <div><strong>Pengemudi / Supir:</strong> {{ $deliveryOrder->nama_supir ?: '-' }}</div>
                        <div><strong>Plat Nomor Kendaraan:</strong> {{ $deliveryOrder->plat_nomor ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian Kain -->
        <div class="mb-4">
            <div class="fw-bold text-dark mb-2">RINCIAN BARANG PENGIRIMAN:</div>
            <div class="table-responsive">
                <table class="sj-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">No</th>
                            <th>Nama Kain & Spesifikasi</th>
                            <th class="text-center">Jenis / Bahan</th>
                            <th class="text-center">Warna</th>
                            <th class="text-center" style="width: 110px;">Jumlah Rol</th>
                            <th class="text-center" style="width: 130px;">Jumlah Meter</th>
                            <th class="text-end" style="width: 140px;">Harga Satuan</th>
                            <th class="text-end" style="width: 150px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryOrder->items as $idx => $item)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $idx + 1 }}</td>
                                <td>
                                    <strong class="text-dark">{{ $item->nama_kain }}</strong>
                                    @if($item->fabric)
                                        <span class="text-muted small ms-1">({{ $item->fabric->kode_kain }})</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->jenis_kain ?: '-' }}</td>
                                <td class="text-center">{{ $item->warna ?: '-' }}</td>
                                <td class="text-center fw-bold">{{ $item->jumlah_rol }} Rol</td>
                                <td class="text-center fw-bold text-primary">{{ number_format($item->jumlah_meter, 1) }} m</td>
                                <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end py-2">TOTAL KESELURUHAN PENGIRIMAN:</td>
                            <td class="text-center py-2 text-dark fs-6">{{ $deliveryOrder->total_rol }} Rol</td>
                            <td class="text-center py-2 text-primary fs-6">{{ number_format($deliveryOrder->total_meter, 1) }} m</td>
                            <td class="text-end py-2 text-muted small">Estimasi Total:</td>
                            <td class="text-end py-2 text-success fs-6">Rp {{ number_format($deliveryOrder->total_nominal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($deliveryOrder->catatan)
            <div class="sj-box mb-4 small">
                <strong>Catatan Pengirim:</strong> {{ $deliveryOrder->catatan }}
            </div>
        @endif

        @if($deliveryOrder->foto_surat_jalan)
            <div class="mb-4">
                <div class="fw-bold text-dark small text-uppercase mb-2">Bukti Kirim (Foto Supir, Foto Kendaraan):</div>
                <div class="d-inline-block p-2 bg-light rounded border shadow-sm">
                    <a href="{{ asset('storage/' . $deliveryOrder->foto_surat_jalan) }}" target="_blank" class="d-block text-decoration-none">
                        <img src="{{ asset('storage/' . $deliveryOrder->foto_surat_jalan) }}" alt="Bukti Kirim (Foto Supir, Foto Kendaraan)" class="rounded border" style="max-height: 200px; width: auto; object-fit: contain;">
                        <div class="text-center mt-1 text-primary small fw-semibold">
                            <i class="bi bi-zoom-in me-1"></i> Klik untuk Perbesar Foto
                        </div>
                    </a>
                </div>
            </div>
        @endif

        <!-- Kolom Tanda Tangan Formal Surat Jalan -->
        <div class="mt-5 pt-4 border-top border-2 border-dark">
            <div class="row text-center g-4">
                <!-- 1. Diterbitkan Oleh (Supplier) -->
                <div class="col-4">
                    <div class="text-muted small fw-semibold mb-2">Diterbitkan Oleh (Supplier),</div>
                    <div class="signature-line"></div>
                    <div class="fw-bold text-dark">{{ $deliveryOrder->user?->name ?? $deliveryOrder->supplier->nama_supplier }}</div>
                    <div class="text-muted small">Mitra Supplier</div>
                </div>

                <!-- 2. Pengemudi / Supir -->
                <div class="col-4">
                    <div class="text-muted small fw-semibold mb-2">Pengemudi / Supir,</div>
                    <div class="signature-line"></div>
                    <div class="fw-bold text-dark">{{ $deliveryOrder->nama_supir ?: '( ................................... )' }}</div>
                    <div class="text-muted small">{{ $deliveryOrder->plat_nomor ?: 'Armada Pengantar' }}</div>
                </div>

                <!-- 3. Diterima Oleh (Gudang DesiTeks) -->
                <div class="col-4">
                    <div class="text-muted small fw-semibold mb-2">Diterima Oleh (Gudang DesiTeks),</div>
                    @if($deliveryOrder->status === 'diterima')
                        <div class="badge bg-success text-white px-2.5 py-1 mb-1">DITERIMA & ACC STOK</div>
                        <div class="fw-bold text-success">{{ $deliveryOrder->receivedBy?->name ?? 'Staf Gudang' }}</div>
                        <div class="text-muted small">Tgl: {{ $deliveryOrder->received_at?->format('d/m/Y H:i') }}</div>
                    @elseif($deliveryOrder->status === 'disetujui_admin' || $deliveryOrder->status === 'dikirim')
                        <div class="badge bg-info text-white px-2.5 py-1 mb-1">ACC ADMIN TOKO</div>
                        <div class="fw-bold text-primary">Proses Cek Gudang</div>
                        <div class="text-muted small">Admin: {{ $deliveryOrder->approvedByAdmin?->name ?? 'Admin Toko' }}</div>
                    @elseif($deliveryOrder->status === 'ditolak')
                        <div class="badge bg-danger text-white px-2.5 py-1 mb-1">DITOLAK</div>
                        <div class="fw-bold text-danger">Pengiriman Ditolak</div>
                        <div class="text-muted small">{{ $deliveryOrder->received_at?->format('d/m/Y H:i') }}</div>
                    @else
                        <div class="signature-line"></div>
                        <div class="fw-bold text-secondary">( Belum Diterima )</div>
                        <div class="text-muted small">Menunggu ACC Admin Toko</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Kirim Barang (Clean Modern Bootstrap Modal) -->
@if(in_array($deliveryOrder->status, ['disetujui_admin', 'dikirim']))
<div class="modal fade" id="modalKirimInfo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <form action="{{ route('supplier.delivery-orders.ship', $deliveryOrder->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom px-4 py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="modal-title fw-bold text-dark m-0">Konfirmasi Keberangkatan Pengiriman</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 11px;"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 26px;">
                        <i class="bi bi-truck-front-fill"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Barang Dalam Perjalanan Ke Gudang?</h6>
                    <p class="text-secondary small mb-0">
                        Pastikan supir/armada pengantar telah membawa barang dan memulai perjalanan menuju gudang DesiTeks (<strong>{{ $deliveryOrder->branch?->nama_cabang ?? 'Gudang Utama' }}</strong>).
                    </p>
                </div>
                <div class="modal-footer border-top px-4 py-2.5 bg-white d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-2 fw-semibold text-secondary" data-bs-dismiss="modal" style="font-size: 13px;">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded-2 shadow-sm" style="font-size: 13px; background: #2563eb; border: none;">
                        <i class="bi bi-send-fill me-1"></i> Ya, Kirim Info Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    function directPrintSuratJalan(url) {
        let printFrame = document.getElementById('directPrintIframe');
        if (!printFrame) {
            printFrame = document.createElement('iframe');
            printFrame.id = 'directPrintIframe';
            printFrame.style.position = 'fixed';
            printFrame.style.right = '0';
            printFrame.style.bottom = '0';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = '0';
            printFrame.style.opacity = '0';
            printFrame.style.pointerEvents = 'none';
            document.body.appendChild(printFrame);
        }
        printFrame.src = url;
    }
</script>
@endpush
