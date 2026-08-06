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
        $query = Stock::with('fabric.category');

        if ($request->filled('search')) {
            $query->whereHas('fabric', function ($q) use ($request) {
                $q->where('kode_kain', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_kain', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('fabric', function ($q) use ($status) {
                if ($status === 'habis') {
                    $q->whereRaw('stocks.stok_meter <= 0');
                } elseif ($status === 'menipis') {
                    $q->whereRaw('stocks.stok_meter > 0 AND stocks.stok_meter <= fabrics.stok_minimum');
                } else {
                    $q->whereRaw('stocks.stok_meter > fabrics.stok_minimum');
                }
            });
        }

        $stocks = $query->join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->select('stocks.*')
            ->orderBy('fabrics.kode_kain')
            ->paginate(10)->withQueryString();

        return view('admin.stocks.index', compact('stocks'));
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
