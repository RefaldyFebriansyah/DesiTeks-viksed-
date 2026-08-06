<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(private StockService $stockService) {}

    /**
     * Proses transaksi penjualan dengan database transaction.
     * Menjalankan: Buat sale → detail → validasi stok → kurangi stok → simpan payment → audit log
     *
     * @param  array $items  [ ['fabric_id'=>..., 'satuan'=>..., 'jumlah'=>..., 'harga_satuan'=>...], ... ]
     * @param  float  $jumlahBayar
     * @param  string $metode
     * @return Sale
     * @throws \Exception jika stok tidak cukup atau pembayaran kurang
     */
    public function prosesTransaksi(array $items, float $jumlahBayar, string $metode): Sale
    {
        return DB::transaction(function () use ($items, $jumlahBayar, $metode) {
            // 1. Hitung total
            $total = 0;
            foreach ($items as $item) {
                $total += $item['harga_satuan'] * $item['jumlah'];
            }

            // 2. Validasi pembayaran
            if ($jumlahBayar < $total) {
                throw new \Exception('Jumlah pembayaran tidak mencukupi.');
            }

            // 3. Buat nomor transaksi unik
            $nomor = $this->generateNomorTransaksi();

            // 4. Buat record sale
            $sale = Sale::create([
                'nomor_transaksi' => $nomor,
                'user_id'         => Auth::id(),
                'total'           => $total,
                'status'          => 'berhasil',
            ]);

            // 5. Simpan detail & kurangi stok
            foreach ($items as $item) {
                $subtotal = $item['harga_satuan'] * $item['jumlah'];

                SaleDetail::create([
                    'sale_id'      => $sale->id,
                    'fabric_id'    => $item['fabric_id'],
                    'satuan'       => $item['satuan'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal'     => $subtotal,
                ]);

                // Kurangi stok (akan throw Exception jika tidak cukup → trigger rollback)
                $this->stockService->kurangiStok(
                    \App\Models\Fabric::findOrFail($item['fabric_id']),
                    $item['satuan'],
                    (float) $item['jumlah'],
                    $sale->id
                );
            }

            // 6. Simpan payment
            $kembalian = $jumlahBayar - $total;
            Payment::create([
                'sale_id'      => $sale->id,
                'jumlah_bayar' => $jumlahBayar,
                'kembalian'    => $kembalian,
                'metode'       => $metode,
            ]);

            // 7. Audit log
            AuditLog::create([
                'user_id'    => Auth::id(),
                'aktivitas'  => "Transaksi penjualan: {$nomor} - Total: Rp " . number_format($total, 0, ',', '.'),
                'model'      => 'Sale',
                'model_id'   => $sale->id,
            ]);

            return $sale->load(['details.fabric', 'payment']);
        });
    }

    private function generateNomorTransaksi(): string
    {
        $prefix  = 'TRX-' . date('Ymd') . '-';
        $last    = Sale::where('nomor_transaksi', 'like', $prefix . '%')
                       ->orderBy('id', 'desc')
                       ->first();
        $seq     = $last ? ((int) substr($last->nomor_transaksi, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
