<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Fabric;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Tambah stok setelah barang masuk.
     */
    public function tambahStok(Fabric $fabric, int $jumlahRol, float $jumlahMeter, int $incomingGoodId): void
    {
        $stock = Stock::firstOrCreate(
            ['fabric_id' => $fabric->id],
            ['stok_rol' => 0, 'stok_meter' => 0, 'updated_at' => now()]
        );

        $stock->stok_rol   += $jumlahRol;
        $stock->stok_meter += $jumlahMeter;
        $stock->updated_at  = now();
        $stock->save();

        StockMovement::create([
            'fabric_id'      => $fabric->id,
            'user_id'        => Auth::id(),
            'jenis'          => 'barang_masuk',
            'jumlah_rol'     => $jumlahRol,
            'jumlah_meter'   => $jumlahMeter,
            'keterangan'     => "Barang masuk ID: {$incomingGoodId}",
            'reference_type' => 'App\Models\IncomingGood',
            'reference_id'   => $incomingGoodId,
        ]);
    }

    /**
     * Kurangi stok saat penjualan. Throw exception jika stok tidak cukup.
     */
    public function kurangiStok(Fabric $fabric, string $satuan, float $jumlah, int $saleId): void
    {
        $stock = Stock::where('fabric_id', $fabric->id)->lockForUpdate()->first();

        if (!$stock) {
            throw new \Exception("Stok untuk kain '{$fabric->nama_kain}' tidak ditemukan.");
        }

        if ($satuan === 'meter') {
            if ($stock->stok_meter < $jumlah) {
                throw new \Exception("Stok meter kain '{$fabric->nama_kain}' tidak mencukupi. Tersedia: {$stock->stok_meter} meter.");
            }
            $stock->stok_meter -= $jumlah;

            StockMovement::create([
                'fabric_id'      => $fabric->id,
                'user_id'        => Auth::id(),
                'jenis'          => 'penjualan',
                'jumlah_rol'     => 0,
                'jumlah_meter'   => $jumlah,
                'keterangan'     => "Penjualan {$jumlah} meter - Sale ID: {$saleId}",
                'reference_type' => 'App\Models\Sale',
                'reference_id'   => $saleId,
            ]);
        } else { // rol
            if ($stock->stok_rol < $jumlah) {
                throw new \Exception("Stok rol kain '{$fabric->nama_kain}' tidak mencukupi. Tersedia: {$stock->stok_rol} rol.");
            }

            // Hitung meter dari rol yang terjual
            $meterPerRol = $stock->stok_rol > 0 ? ($stock->stok_meter / $stock->stok_rol) : 0;
            $totalMeter  = $meterPerRol * $jumlah;

            $stock->stok_rol   -= (int) $jumlah;
            $stock->stok_meter -= $totalMeter;

            StockMovement::create([
                'fabric_id'      => $fabric->id,
                'user_id'        => Auth::id(),
                'jenis'          => 'penjualan',
                'jumlah_rol'     => (int) $jumlah,
                'jumlah_meter'   => $totalMeter,
                'keterangan'     => "Penjualan {$jumlah} rol (~{$totalMeter} meter) - Sale ID: {$saleId}",
                'reference_type' => 'App\Models\Sale',
                'reference_id'   => $saleId,
            ]);
        }

        if ($stock->stok_meter < 0) $stock->stok_meter = 0;
        if ($stock->stok_rol < 0)   $stock->stok_rol   = 0;
        $stock->updated_at = now();
        $stock->save();
    }

    /**
     * Penyesuaian stok manual oleh admin.
     */
    public function sesuaikanStok(Fabric $fabric, int $rolBaru, float $meterBaru, string $keterangan): void
    {
        $stock = Stock::firstOrCreate(
            ['fabric_id' => $fabric->id],
            ['stok_rol' => 0, 'stok_meter' => 0, 'updated_at' => now()]
        );

        $stock->stok_rol   = $rolBaru;
        $stock->stok_meter = $meterBaru;
        $stock->updated_at = now();
        $stock->save();

        StockMovement::create([
            'fabric_id'    => $fabric->id,
            'user_id'      => Auth::id(),
            'jenis'        => 'penyesuaian',
            'jumlah_rol'   => $rolBaru,
            'jumlah_meter' => $meterBaru,
            'keterangan'   => $keterangan ?: 'Penyesuaian stok manual',
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'aktivitas'  => "Penyesuaian stok kain: {$fabric->nama_kain}",
            'model'      => 'Stock',
            'model_id'   => $stock->id,
        ]);
    }
}
