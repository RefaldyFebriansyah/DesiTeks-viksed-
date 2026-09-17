<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fabric;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = Stock::with('fabric.category')
            ->join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->select('stocks.*');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fabrics.kode_kain', 'like', '%' . $search . '%')
                  ->orWhere('fabrics.nama_kain', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'habis') {
                $query->where('stocks.stok_rol', '<=', 0)
                      ->where('stocks.stok_meter', '<=', 0);
            } elseif ($status === 'menipis') {
                $query->where(function ($q) {
                    $q->where('stocks.stok_rol', '>', 0)
                      ->orWhere('stocks.stok_meter', '>', 0);
                })->where('stocks.stok_rol', '<=', 5);
            } elseif ($status === 'penuh') {
                $query->where('fabrics.stok_maksimum', '>', 0)
                      ->whereColumn('stocks.stok_rol', '>=', 'fabrics.stok_maksimum');
            } elseif ($status === 'aman') {
                $query->where('stocks.stok_rol', '>', 5)
                      ->where(function ($q) {
                          $q->whereNull('fabrics.stok_maksimum')
                            ->orWhere('fabrics.stok_maksimum', '<=', 0)
                            ->orWhereColumn('stocks.stok_rol', '<', 'fabrics.stok_maksimum');
                      });
            }
        }

        $stats = Stock::join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->selectRaw("
                COUNT(*) as total_semua,
                COUNT(CASE WHEN stocks.stok_rol <= 0 AND stocks.stok_meter <= 0 THEN 1 END) as count_habis,
                COUNT(CASE WHEN (stocks.stok_rol > 0 OR stocks.stok_meter > 0) AND stocks.stok_rol <= 5 THEN 1 END) as count_menipis,
                COUNT(CASE WHEN fabrics.stok_maksimum > 0 AND stocks.stok_rol >= fabrics.stok_maksimum THEN 1 END) as count_penuh,
                COUNT(CASE WHEN stocks.stok_rol > 5 AND (fabrics.stok_maksimum IS NULL OR fabrics.stok_maksimum <= 0 OR stocks.stok_rol < fabrics.stok_maksimum) THEN 1 END) as count_aman
            ")
            ->first();

        $countHabis   = (int) ($stats->count_habis ?? 0);
        $countMenipis = (int) ($stats->count_menipis ?? 0);
        $countPenuh   = (int) ($stats->count_penuh ?? 0);
        $countAman    = (int) ($stats->count_aman ?? 0);
        $totalSemua   = (int) ($stats->total_semua ?? 0);

        // Calculate total meter across all active stocks for Gudang capacity widget
        $allStocks = Stock::with('fabric')
            ->join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->select('stocks.*')
            ->get();

        $totalMeterGudang   = $allStocks->sum(fn($s) => $s->total_meter);
        $maxStokGudangTotal = (int) \App\Models\Setting::getVal('max_stok_gudang_total', '10000');

        $stocks = $query->orderBy('fabrics.kode_kain')
            ->paginate(15)->withQueryString();

        return view('admin.stocks.index', compact(
            'stocks', 
            'countHabis', 
            'countMenipis', 
            'countPenuh',
            'countAman', 
            'totalSemua',
            'totalMeterGudang',
            'maxStokGudangTotal'
        ));
    }

    public function adjust(Request $request, Fabric $fabric)
    {
        $request->validate([
            'stok_rol'   => 'required|integer|min:0',
            'stok_meter' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $this->stockService->sesuaikanStok(
            $fabric,
            $request->stok_rol,
            $request->stok_meter,
            $request->keterangan
        );

        return back()->with('success', "Stok kain {$fabric->nama_kain} berhasil disesuaikan.");
    }

    public function updateMaxStock(Request $request, Fabric $fabric)
    {
        $request->validate([
            'stok_maksimum'     => 'required|integer|min:0',
            'terapkan_ke_semua' => 'nullable|boolean',
        ]);

        $maxVal = (int) $request->stok_maksimum;

        if ($request->boolean('terapkan_ke_semua')) {
            Fabric::query()->update(['stok_maksimum' => $maxVal]);

            \App\Models\AuditLog::create([
                'user_id'   => \Illuminate\Support\Facades\Auth::id(),
                'aktivitas' => "Mengatur max stock gudang seluruh kain menjadi {$maxVal} rol",
                'model'     => 'Fabric',
                'model_id'  => 0,
            ]);

            return back()->with('success', "Batas Stok Maksimum gudang untuk SELURUH KAIN berhasil diubah menjadi {$maxVal} rol.");
        }

        $oldMax = $fabric->stok_maksimum;
        $fabric->update([
            'stok_maksimum' => $maxVal,
        ]);

        \App\Models\AuditLog::create([
            'user_id'   => \Illuminate\Support\Facades\Auth::id(),
            'aktivitas' => "Mengatur max stock gudang kain {$fabric->nama_kain} dari {$oldMax} rol menjadi {$maxVal} rol",
            'model'     => 'Fabric',
            'model_id'  => $fabric->id,
        ]);

        return back()->with('success', "Batas Stok Maksimum gudang untuk kain {$fabric->nama_kain} berhasil diubah menjadi {$maxVal} rol.");
    }

    public function updateTotalMaxStock(Request $request)
    {
        $request->validate([
            'max_stok_gudang_total'  => 'required|integer|min:0',
            'max_stok_per_item_all'  => 'nullable|integer|min:0',
            'terapkan_ke_semua_item' => 'nullable|boolean',
        ]);

        \App\Models\Setting::setVal('max_stok_gudang_total', (string) $request->max_stok_gudang_total);

        if ($request->boolean('terapkan_ke_semua_item') && $request->filled('max_stok_per_item_all')) {
            Fabric::query()->update(['stok_maksimum' => (int) $request->max_stok_per_item_all]);
        }

        \App\Models\AuditLog::create([
            'user_id'   => \Illuminate\Support\Facades\Auth::id(),
            'aktivitas' => "Mengubah Total Kapasitas Keseluruhan Gudang Kain menjadi {$request->max_stok_gudang_total} meter",
            'model'     => 'Setting',
            'model_id'  => 0,
        ]);

        return back()->with('success', "Kapasitas Total Keseluruhan Gudang Kain berhasil diperbarui.");
    }
}
