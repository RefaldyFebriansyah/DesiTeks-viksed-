<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Models\Category;
use App\Models\Fabric;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(private SaleService $saleService) {}

    public function pos(Request $request)
    {
        $query = Fabric::with(['stock', 'category'])->where('status', 'aktif');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($qb) => $qb->where('nama_kain','like',"%$q%")->orWhere('kode_kain','like',"%$q%"));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $fabrics    = $query->orderBy('nama_kain')->get();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('admin.sales.pos', compact('fabrics', 'categories'));
    }

    public function store(SaleRequest $request)
    {
        try {
            $sale = $this->saleService->prosesTransaksi(
                $request->items,
                (float) $request->jumlah_bayar,
                $request->metode
            );
            return redirect()->route('admin.sales.success', $sale->id);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function success(Sale $sale)
    {
        $sale->load(['details.fabric', 'payment', 'user']);
        return view('sales.success', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['details.fabric', 'payment', 'user']);
        return view('sales.receipt', compact('sale'));
    }
}
