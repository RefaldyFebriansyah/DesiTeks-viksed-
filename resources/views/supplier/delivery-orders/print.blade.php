<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $deliveryOrder->nomor_surat_jalan }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
            background: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }
        .header-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #000000;
            text-transform: uppercase;
        }
        .print-box {
            border: 1px solid #000000;
            padding: 10px 12px;
            background: #ffffff;
        }
        .table-print {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .table-print th {
            background-color: #f1f5f9 !important;
            color: #000000 !important;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            padding: 7px 8px;
            border: 1px solid #000000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .table-print td {
            padding: 7px 8px;
            border: 1px solid #000000 !important;
            color: #000000;
            font-size: 12px;
        }
        .table-print tfoot td {
            background-color: #f8fafc !important;
            font-weight: 700;
            border: 1px solid #000000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .signature-line-print {
            border-bottom: 1.5px solid #000000;
            width: 80%;
            margin: 55px auto 4px auto;
        }
        .signature-block {
            page-break-inside: avoid;
        }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Kop Surat Resmi (Identitas Supplier Pengirim) -->
    <div class="row align-items-center pb-3 mb-3 border-bottom border-2 border-dark">
        <div class="col-7">
            <h4 class="fw-bold mb-0 text-dark text-uppercase" style="letter-spacing: -0.5px;">{{ $deliveryOrder->supplier->nama_supplier }}</h4>
            <div class="fw-bold text-dark small text-uppercase mb-1">Pabrik & Supplier Tekstil Rekanan</div>
            <div class="text-dark small" style="font-size: 11px;">
                Kode: <strong>{{ $deliveryOrder->supplier->kode_supplier }}</strong>
                @if($deliveryOrder->supplier->asal_kota) | {{ $deliveryOrder->supplier->asal_kota }} @endif
                @if($deliveryOrder->supplier->no_telepon) | Telp: +62{{ $deliveryOrder->supplier->no_telepon }} @endif
            </div>
            @if($deliveryOrder->supplier->alamat)
                <div class="text-dark small" style="font-size: 11px;">{{ $deliveryOrder->supplier->alamat }}</div>
            @endif
        </div>
        <div class="col-5 text-end">
            <div class="header-title">SURAT JALAN</div>
            <div class="fw-bold text-dark fs-6 mt-1">No: {{ $deliveryOrder->nomor_surat_jalan }}</div>
            <div class="text-dark small">Tanggal Kirim: <strong>{{ $deliveryOrder->tanggal_kirim->format('d/m/Y') }}</strong></div>
        </div>
    </div>

    <!-- Info Penerima & Ekspedisi (2 Kolom Formal) -->
    <div class="row g-3 mb-3">
        <div class="col-6">
            <div class="print-box h-100">
                <div class="fw-bold small text-uppercase border-bottom border-dark pb-1 mb-2">KEPADA YTH. (PENERIMA):</div>
                <div class="fw-bold fs-6">PT. DESITEKS SEJAHTERA NUSA</div>
                <div class="fw-semibold small">{{ $deliveryOrder->branch?->nama_cabang ?? 'Gudang Utama DesiTeks' }}</div>
                <div class="small">{{ $deliveryOrder->branch?->alamat ?? 'Jl. Kebon Jati No. 45, Bandung, Jawa Barat' }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="print-box h-100">
                <div class="fw-bold small text-uppercase border-bottom border-dark pb-1 mb-2">PENGANGKUTAN & EKSPEDISI:</div>
                <div class="small">
                    <div><strong>Supir / Pengemudi:</strong> {{ $deliveryOrder->nama_supir ?: '-' }}</div>
                    <div><strong>No. Plat Kendaraan:</strong> {{ $deliveryOrder->plat_nomor ?: '-' }}</div>
                    <div><strong>Armada / Ekspedisi:</strong> {{ $deliveryOrder->ekspedisi ?: 'Truk / Pengiriman Mandiri' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rincian Kain -->
    <div class="mb-3">
        <div class="fw-bold text-dark mb-1">RINCIAN BARANG PENGIRIMAN:</div>
        <table class="table-print">
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">No</th>
                    <th>Nama Kain & Spesifikasi</th>
                    <th class="text-center" style="width: 100px;">Jenis / Bahan</th>
                    <th class="text-center" style="width: 90px;">Warna</th>
                    <th class="text-center" style="width: 80px;">Jml Rol</th>
                    <th class="text-center" style="width: 100px;">Total Meter</th>
                    <th class="text-end" style="width: 110px;">Harga Satuan</th>
                    <th class="text-end" style="width: 130px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryOrder->items as $idx => $item)
                    <tr>
                        <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->nama_kain }}</strong>
                            @if($item->fabric)
                                <span class="small text-dark">({{ $item->fabric->kode_kain }})</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->jenis_kain ?: '-' }}</td>
                        <td class="text-center">{{ $item->warna ?: '-' }}</td>
                        <td class="text-center fw-bold">{{ $item->jumlah_rol }} Rol</td>
                        <td class="text-center fw-bold">{{ number_format($item->jumlah_meter, 1) }} m</td>
                        <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-bold">TOTAL KESELURUHAN PENGIRIMAN:</td>
                    <td class="text-center fw-bold">{{ $deliveryOrder->total_rol }} Rol</td>
                    <td class="text-center fw-bold">{{ number_format($deliveryOrder->total_meter, 1) }} m</td>
                    <td class="text-end small fw-bold">Estimasi Total:</td>
                    <td class="text-end fw-bold fs-6">Rp {{ number_format($deliveryOrder->total_nominal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($deliveryOrder->catatan)
        <div class="print-box mb-3 small">
            <strong>Catatan Pengiriman:</strong> {{ $deliveryOrder->catatan }}
        </div>
    @endif

    <!-- Tanda Tangan Formal Dokumen Cetak (3 Kolom) -->
    <div class="signature-block mt-4 pt-2">
        <div class="row text-center g-3">
            <div class="col-4">
                <div class="fw-bold">Pengirim (Supplier),</div>
                <div class="signature-line-print"></div>
                <div class="fw-bold">{{ $deliveryOrder->user?->name ?? $deliveryOrder->supplier->nama_supplier }}</div>
                <div class="small">Mitra Supplier</div>
            </div>
            <div class="col-4">
                <div class="fw-bold">Pengemudi / Supir,</div>
                <div class="signature-line-print"></div>
                <div class="fw-bold">{{ $deliveryOrder->nama_supir ?: '( ................................... )' }}</div>
                <div class="small">{{ $deliveryOrder->plat_nomor ?: 'Armada Pengantar' }}</div>
            </div>
            <div class="col-4">
                <div class="fw-bold">Diterima Oleh (Gudang DesiTeks),</div>
                <div class="signature-line-print"></div>
                <div class="fw-bold">{{ $deliveryOrder->receivedBy?->name ?: '( ................................... )' }}</div>
                <div class="small">{{ $deliveryOrder->received_at ? $deliveryOrder->received_at->format('d/m/Y H:i') : 'Tim Logistik Gudang' }}</div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.focus();
                window.print();
            }, 150);
        });
    </script>
</body>
</html>
