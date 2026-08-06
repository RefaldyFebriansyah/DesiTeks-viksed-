<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk — {{ $sale->nomor_transaksi }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; color: #000; background: #fff; }
        .receipt { max-width: 300px; margin: 0 auto; padding: 20px 10px; }
        .header { text-align:center; margin-bottom: 12px; }
        .brand { font-size: 20px; font-weight: 900; letter-spacing: 2px; }
        .slogan { font-size: 10px; margin-top: 2px; }
        .divider { border: none; border-top: 1px dashed #000; margin: 8px 0; }
        .info-row { display: flex; justify-content: space-between; margin: 3px 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; margin: 8px 0; }
        th { border-bottom: 1px solid #000; padding: 4px 2px; text-align: left; font-size: 10px; }
        td { padding: 3px 2px; vertical-align: top; }
        .text-right { text-align: right; }
        .total-row { font-weight: 700; font-size: 13px; }
        .footer { text-align:center; font-size: 10px; margin-top: 12px; }
        .no-print { text-align:center; margin: 20px; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
<div class="no-print">
    <button onclick="window.print()" style="padding:8px 20px;background:#0f2744;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px">🖨️ Cetak Struk</button>
    <button onclick="window.close()" style="padding:8px 20px;background:#64748b;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px;margin-left:8px">✕ Tutup</button>
</div>

<div class="receipt">
    <div class="header">
        <img src="{{ asset('images/logo_transparent.png') }}" alt="DesiTeks Logo" style="max-width:130px;height:auto;margin-bottom:4px">
    </div>

    <hr class="divider">

    <div class="info-row"><span>No. Transaksi</span><span>{{ $sale->nomor_transaksi }}</span></div>
    <div class="info-row"><span>Tanggal</span><span>{{ $sale->created_at->format('d/m/Y H:i') }}</span></div>
    <div class="info-row"><span>Kasir</span><span>{{ $sale->user->name }}</span></div>

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

    <div class="info-row total-row"><span>TOTAL</span><span>Rp {{ number_format($sale->total,0,',','.') }}</span></div>
    <div class="info-row"><span>Pembayaran ({{ ucfirst($sale->payment->metode) }})</span><span>Rp {{ number_format($sale->payment->jumlah_bayar,0,',','.') }}</span></div>
    <div class="info-row total-row"><span>KEMBALIAN</span><span>Rp {{ number_format($sale->payment->kembalian,0,',','.') }}</span></div>

    <hr class="divider">

    <div class="footer">
        <div>Terima kasih telah berbelanja di DesiTeks!</div>
        <div>Barang yang sudah dibeli tidak dapat dikembalikan.</div>
    </div>
</div>
</body>
</html>
