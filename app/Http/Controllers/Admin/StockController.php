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
                $query->where('stocks.stok_rol', '<=', 5)
                      ->where(function ($q) {
                          $q->where('stocks.stok_rol', '>', 0)
                            ->orWhere('stocks.stok_meter', '>', 0);
                      });
            } elseif ($status === 'aman') {
                $query->where('stocks.stok_rol', '>', 5);
            }
        }

        $stats = Stock::join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->selectRaw("
                COUNT(*) as total_semua,
                COUNT(CASE WHEN stocks.stok_rol <= 0 AND stocks.stok_meter <= 0 THEN 1 END) as count_habis,
                COUNT(CASE WHEN stocks.stok_rol <= 5 AND (stocks.stok_rol > 0 OR stocks.stok_meter > 0) THEN 1 END) as count_menipis,
                COUNT(CASE WHEN stocks.stok_rol > 5 THEN 1 END) as count_aman
            ")
            ->first();

        $countHabis   = (int) ($stats->count_habis ?? 0);
        $countMenipis = (int) ($stats->count_menipis ?? 0);
        $countAman    = (int) ($stats->count_aman ?? 0);
        $totalSemua   = (int) ($stats->total_semua ?? 0);

        $stocks = $query->orderBy('fabrics.kode_kain')
            ->paginate(15)->withQueryString();

        return view('admin.stocks.index', compact('stocks', 'countHabis', 'countMenipis', 'countAman', 'totalSemua'));
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
}
