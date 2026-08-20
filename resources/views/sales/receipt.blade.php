@php
    $namaToko = \App\Models\Setting::getVal('nama_toko', 'DesiTeks');
    $alamatToko = \App\Models\Setting::getVal('alamat_toko', '');
    $teleponToko = \App\Models\Setting::getVal('telepon_toko', '');
    $catatanStruk = \App\Models\Setting::getVal('catatan_struk', 'Terima kasih telah berbelanja!');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk — {{ $sale->nomor_transaksi }}</title>
    <!-- Google Fonts for premium look -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        
        /* Screen View Styles */
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: radial-gradient(circle at top, #f8fafc 0%, #cbd5e1 100%); 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }
        
        /* Floating Glassmorphic Control Bar */
        .no-print { 
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 10px 16px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 20px rgba(15, 39, 68, 0.05);
            z-index: 10;
        }
        .ctrl-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13.5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-print {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(15, 39, 68, 0.25);
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 39, 68, 0.35);
        }
        .btn-close {
            background: #fff;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }
        .btn-close:hover {
            background: #f1f5f9;
            color: #334155;
            transform: translateY(-2px);
        }

        /* Skeuomorphic Paper Receipt Card */
        .receipt-card { 
            background: #fff; 
            width: 340px; 
            padding: 30px 24px 40px 24px; 
            border-radius: 12px 12px 0 0;
            box-shadow: 0 20px 40px rgba(15, 39, 68, 0.08);
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.03);
            border-bottom: none;
        }
        
        /* Jagged/Perforated Bottom Edge using Repeating Linear Gradient */
        .receipt-card::after {
            content: '';
            display: block;
            position: absolute;
            bottom: -12px;
            left: 0;
            width: 100%;
            height: 12px;
            background: 
                linear-gradient(-45deg, transparent 6px, #fff 0), 
                linear-gradient(45deg, transparent 6px, #fff 0);
            background-size: 12px 12px;
        }
        
        /* Receipt Content Styling (Mono-spaced Courier Prime for real ticket vibe) */
        .receipt-content {
            font-family: 'Courier Prime', 'Courier New', monospace;
            font-size: 12px;
            color: #000;
            line-height: 1.4;
        }
        .header { text-align:center; margin-bottom: 16px; }
        .brand { font-size: 22px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .slogan { font-size: 10.5px; color: #334155; margin-top: 1px; }
        .divider { border: none; border-top: 1px dashed #000; margin: 10px 0; }
        .info-row { display: flex; justify-content: space-between; margin: 4px 0; font-size: 11.5px; }
        table { width: 100%; border-collapse: collapse; font-size: 11.5px; margin: 10px 0; }
        th { border-bottom: 1px solid #000; padding: 6px 2px; text-align: left; font-size: 10.5px; font-weight: 700; }
        td { padding: 4px 2px; vertical-align: top; }
        .text-right { text-align: right; }
        .total-row { font-weight: 700; font-size: 13.5px; }
        .footer { text-align:center; font-size: 10.5px; margin-top: 16px; line-height: 1.5; word-break: break-word; }

        /* Print Media Rules */
        @media print {
            .no-print { display: none !important; }
            body { 
                background: #fff !important; 
                padding: 0 !important;
                min-height: auto !important;
                display: block !important;
            }
            .receipt-card { 
                box-shadow: none !important; 
                border: none !important; 
                padding: 0 !important; 
                width: 100% !important; 
            }
            .receipt-card::after { display: none !important; }
            .receipt-content { font-size: 12px !important; }
        }
    </style>
</head>
<body>
<div class="no-print">
    <button class="ctrl-btn btn-print" onclick="window.print()">
        <i class="bi bi-printer-fill"></i> Cetak Struk
    </button>
    <button class="ctrl-btn btn-close" onclick="window.close()">
        <i class="bi bi-x-lg"></i> Tutup Halaman
    </button>
</div>

<div class="receipt-card">
    <div class="receipt-content">
        <div class="header">
            <div class="brand">{{ $namaToko }}</div>
            @if($alamatToko)
                <div class="slogan">{{ $alamatToko }}</div>
            @endif
            @if($teleponToko)
                <div class="slogan">Telp: {{ $teleponToko }}</div>
            @endif
        </div>

        <hr class="divider">

        <div class="info-row"><span>No. Transaksi</span><span>{{ $sale->nomor_transaksi }}</span></div>
        <div class="info-row"><span>Tanggal</span><span>{{ $sale->created_at->format('d/m/Y H:i') }}</span></div>
        <div class="info-row"><span>Kasir</span><span>{{ $sale->user->name }}</span></div>
        @if($sale->customer)
            <div class="info-row"><span>Pelanggan</span><span>{{ $sale->customer->nama }} ({{ ucfirst($sale->customer->tipe) }})</span></div>
        @endif

        <hr class="divider">

        <table>
            <thead>
                <tr>
                    <th>Nama Kain</th>
                    <th class="text-right">Jml</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            @foreach($sale->details as $d)
                <tr>
                    <td>{{ $d->fabric->nama_kain }}</td>
                    <td class="text-right">{{ number_format($d->jumlah, $d->satuan==='meter'?1:0) }} {{ $d->satuan }}</td>
                    <td class="text-right">{{ number_format($d->harga_satuan,0,',','.') }}</td>
                    <td class="text-right">{{ number_format($d->subtotal,0,',','.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <hr class="divider">

        @if($sale->diskon > 0)
            @php
                $subtotalBelanja = $sale->details->sum('subtotal');
            @endphp
            <div class="info-row"><span>Subtotal</span><span>Rp {{ number_format($subtotalBelanja,0,',','.') }}</span></div>
            <div class="info-row"><span>Diskon Member</span><span>-Rp {{ number_format($sale->diskon,0,',','.') }}</span></div>
        @endif
        
        @if($sale->pajak > 0)
            <div class="info-row"><span>Pajak</span><span>Rp {{ number_format($sale->pajak,0,',','.') }}</span></div>
        @endif

        <div class="info-row total-row"><span>TOTAL</span><span>Rp {{ number_format($sale->total,0,',','.') }}</span></div>
        <div class="info-row"><span>Pembayaran ({{ ucfirst($sale->payment->metode) }})</span><span>Rp {{ number_format($sale->payment->jumlah_bayar,0,',','.') }}</span></div>
        <div class="info-row total-row"><span>KEMBALIAN</span><span>Rp {{ number_format($sale->payment->kembalian,0,',','.') }}</span></div>

        <hr class="divider">

        <div class="footer">
            {!! nl2br(e($catatanStruk)) !!}
        </div>
    </div>
</div>
</body>
</html>
