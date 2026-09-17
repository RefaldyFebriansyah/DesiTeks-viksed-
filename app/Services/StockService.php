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
    private function getActiveBranchId(): int
    {
        return session('active_branch_id') ?? Auth::user()?->branch_id ?? 1;
    }

    /**
     * Tambah stok setelah barang masuk.
     */
    public function tambahStok(Fabric $fabric, int $jumlahRol, float $jumlahMeter, int $incomingGoodId): void
    {
        $branchId = $this->getActiveBranchId();
        $stock = Stock::firstOrCreate(
            ['fabric_id' => $fabric->id, 'branch_id' => $branchId],
            ['stok_rol' => 0, 'stok_meter' => 0, 'updated_at' => now()]
        );

        $meterPerRol = (float) ($fabric->meter_per_rol > 0 ? $fabric->meter_per_rol : 50);

        // Hitung eceran murni yang masuk (total meteran dikurangi meteran yang ada di dalam rol utuh)
        $looseMeterMasuk = $jumlahMeter - ($jumlahRol * $meterPerRol);
        if ($looseMeterMasuk < 0) $looseMeterMasuk = 0;

        $maxStok = (int) ($fabric->stok_maksimum > 0 ? $fabric->stok_maksimum : 25);
        if ($maxStok > 0 && ($stock->stok_rol + $jumlahRol) > $maxStok) {
            $totalMencoba = $stock->stok_rol + $jumlahRol;
            throw new \Exception("Kapasitas gudang untuk kain '{$fabric->nama_kain}' melebihi batas (Maksimal {$maxStok} rol per item). Stok saat ini: {$stock->stok_rol} rol, mencoba menjadi {$totalMencoba} rol.");
        }

        $stock->stok_rol   += $jumlahRol;
        $stock->stok_meter += $looseMeterMasuk;
        $stock->updated_at  = now();
        $stock->save();

        StockMovement::create([
            'fabric_id'      => $fabric->id,
            'user_id'        => Auth::id(),
            'jenis'          => 'barang_masuk',
            'jumlah_rol'     => $jumlahRol,
            'jumlah_meter'   => $jumlahMeter,
            'keterangan'     => "Penerimaan barang masuk (Gudang #{$incomingGoodId})",
            'reference_type' => 'App\Models\IncomingGood',
            'reference_id'   => $incomingGoodId,
        ]);
    }

    /**
     * Kurangi stok saat penjualan. Throw exception jika stok tidak cukup.
     */
    public function kurangiStok(Fabric $fabric, string $satuan, float $jumlah, int $saleId): void
    {
        $branchId = $this->getActiveBranchId();
        $stock = Stock::where('fabric_id', $fabric->id)
            ->where('branch_id', $branchId)
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            // Fallback try without branch filter if exact match missing
            $stock = Stock::where('fabric_id', $fabric->id)->lockForUpdate()->first();
        }

        if (!$stock) {
            throw new \Exception("Stok untuk kain '{$fabric->nama_kain}' tidak ditemukan.");
        }

        // Ambil standar meter per rol dari database, default ke 50 jika kosong/nol
        $meterPerRol = (float) ($fabric->meter_per_rol > 0 ? $fabric->meter_per_rol : 50);

        if ($satuan === 'meter') {
            // Total meteran tersedia = sisa eceran + (rol utuh * meter_per_rol)
            $totalAvailable = (float) ($stock->stok_meter + ($stock->stok_rol * $meterPerRol));

            if ($totalAvailable < $jumlah) {
                throw new \Exception("Total stok meter kain '{$fabric->nama_kain}' tidak mencukupi. Tersedia: {$totalAvailable} meter.");
            }

            // Jika eceran tidak cukup, buka rol utuh untuk dijadikan eceran
            if ($stock->stok_meter < $jumlah) {
                $needed = $jumlah - $stock->stok_meter;
                $rollsToOpen = (int) ceil($needed / $meterPerRol);

                // Kurangi rol utuh dan tambahkan ke meteran eceran
                $stock->stok_rol -= $rollsToOpen;
                $stock->stok_meter += ($rollsToOpen * $meterPerRol);
            }

            // Potong dari sisa eceran
            $stock->stok_meter -= $jumlah;

            $keterangan = "Penjualan eceran {$jumlah} m (Nota #{$saleId})";
            if (isset($rollsToOpen) && $rollsToOpen > 0) {
                $keterangan .= " - Potong {$rollsToOpen} rol ke eceran";
            }

            StockMovement::create([
                'fabric_id'      => $fabric->id,
                'user_id'        => Auth::id(),
                'jenis'          => 'penjualan',
                'jumlah_rol'     => 0,
                'jumlah_meter'   => $jumlah,
                'keterangan'     => $keterangan,
                'reference_type' => 'App\Models\Sale',
                'reference_id'   => $saleId,
            ]);
        } else { // rol
            if ($stock->stok_rol < $jumlah) {
                throw new \Exception("Stok rol kain '{$fabric->nama_kain}' tidak mencukupi. Tersedia: {$stock->stok_rol} rol.");
            }

            // Kurangi rol utuh saja, stok_meter (eceran) tetap utuh karena yang dijual adalah rol utuh tertutup
            $stock->stok_rol -= (int) $jumlah;

            StockMovement::create([
                'fabric_id'      => $fabric->id,
                'user_id'        => Auth::id(),
                'jenis'          => 'penjualan',
                'jumlah_rol'     => (int) $jumlah,
                'jumlah_meter'   => $jumlah * $meterPerRol,
                'keterangan'     => "Penjualan grosir {$jumlah} rol (" . ($jumlah * $meterPerRol) . " m) (Nota #{$saleId})",
                'reference_type' => 'App\Models\Sale',
                'reference_id'   => $saleId,
            ]);
        }

        if ($stock->stok_meter < 0) $stock->stok_meter = 0;
        if ($stock->stok_rol < 0)   $stock->stok_rol   = 0;
        $stock->updated_at = now();
        $stock->save();

        // Trigger notifikasi jika stok rol <= stok_minimum atau <= 0
        $minStok = (int) ($fabric->stok_minimum > 0 ? $fabric->stok_minimum : 5);
        if ($stock->stok_rol <= $minStok) {
            $statusText = ($stock->stok_rol <= 0 && $stock->stok_meter <= 0) ? 'HABIS' : 'MENIPIS';
            \App\Models\AppNotification::create([
                'type'    => 'stok_menipis',
                'title'   => "Peringatan Stok {$statusText}",
                'message' => "Stok kain '{$fabric->nama_kain}' {$statusText} (tersisa {$stock->stok_rol} rol / " . number_format($stock->stok_meter, 1) . "m eceran).",
                'link'    => route('admin.stocks.index', ['status' => strtolower($statusText)]),
            ]);
        }
    }

    /**
     * Penyesuaian stok manual oleh admin.
     */
    public function sesuaikanStok(Fabric $fabric, int $rolBaru, float $meterBaru, string $keterangan): void
    {
        $branchId = $this->getActiveBranchId();
        $stock = Stock::firstOrCreate(
            ['fabric_id' => $fabric->id, 'branch_id' => $branchId],
            ['stok_rol' => 0, 'stok_meter' => 0, 'updated_at' => now()]
        );

        $maxStok = (int) ($fabric->stok_maksimum > 0 ? $fabric->stok_maksimum : 25);
        if ($maxStok > 0 && $rolBaru > $maxStok) {
            throw new \Exception("Stok rol kain '{$fabric->nama_kain}' tidak boleh melebihi {$maxStok} rol karena kapasitas gudang terbatas (Maksimal {$maxStok} rol per item).");
        }

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

    /**
     * Kembalikan stok saat transaksi dibatalkan.
     */
    public function kembalikanStok(\App\Models\Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            $branchId = $this->getActiveBranchId();

            // Cari pergerakan stok penjualan yang berkaitan dengan transaksi ini
            $movements = StockMovement::where('reference_type', 'App\Models\Sale')
                ->where('reference_id', $sale->id)
                ->get();

            if ($movements->isNotEmpty()) {
                foreach ($movements as $movement) {
                    $stock = Stock::firstOrCreate(
                        ['fabric_id' => $movement->fabric_id, 'branch_id' => $branchId],
                        ['stok_rol' => 0, 'stok_meter' => 0, 'updated_at' => now()]
                    );

                    // Tambahkan kembali stok yang dikurangi sebelumnya
                    $stock->stok_rol   += $movement->jumlah_rol;
                    $stock->stok_meter += $movement->jumlah_meter;
                    $stock->updated_at  = now();
                    $stock->save();

                    // Catat log pergerakan stok pemulihan (retur/pembatalan)
                    StockMovement::create([
                        'fabric_id'      => $movement->fabric_id,
                        'user_id'        => Auth::id() ?? $sale->user_id, // Gunakan ID kasir pembuat jika Auth null
                        'jenis'          => 'penyesuaian',
                        'jumlah_rol'     => $movement->jumlah_rol,
                        'jumlah_meter'   => $movement->jumlah_meter,
                        'keterangan'     => "Pengembalian stok (Batal TRX: {$sale->nomor_transaksi})",
                        'reference_type' => 'App\Models\Sale',
                        'reference_id'   => $sale->id,
                    ]);
                }
            } else {
                // Fallback jika tidak ada data di stock_movements (misal data seeder awal)
                foreach ($sale->details as $detail) {
                    $stock = Stock::firstOrCreate(
                        ['fabric_id' => $detail->fabric_id, 'branch_id' => $branchId],
                        ['stok_rol' => 0, 'stok_meter' => 0, 'updated_at' => now()]
                    );

                    if ($detail->satuan === 'meter') {
                        $stock->stok_meter += $detail->jumlah;
                        $rol   = 0;
                        $meter = $detail->jumlah;
                    } else { // rol
                        $stock->stok_rol   += (int) $detail->jumlah;
                        $rol   = (int) $detail->jumlah;
                        // Estimasi meter per rol
                        $meterPerRol = $stock->stok_rol > 0 ? ($stock->stok_meter / $stock->stok_rol) : 0;
                        $meter = $meterPerRol * $detail->jumlah;
                        $stock->stok_meter += $meter;
                    }

                    $stock->updated_at = now();
                    $stock->save();

                    StockMovement::create([
                        'fabric_id'      => $detail->fabric_id,
                        'user_id'        => Auth::id() ?? $sale->user_id,
                        'jenis'          => 'penyesuaian',
                        'jumlah_rol'     => $rol,
                        'jumlah_meter'   => $meter,
                        'keterangan'     => "Pengembalian stok (Batal TRX: {$sale->nomor_transaksi}) [Fallback]",
                        'reference_type' => 'App\Models\Sale',
                        'reference_id'   => $sale->id,
                    ]);
                }
            }
        });
    }
}
