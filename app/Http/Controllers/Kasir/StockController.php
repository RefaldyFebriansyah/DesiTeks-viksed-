<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::with('fabric.category')
            ->join('fabrics','stocks.fabric_id','=','fabrics.id')
            ->where('fabrics.status','aktif')
            ->select('stocks.*');

        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('fabrics.nama_kain','like','%'.$request->search.'%')
                ->orWhere('fabrics.kode_kain','like','%'.$request->search.'%'));
        }

        $stocks = $query->orderBy('fabrics.nama_kain')->paginate(10)->withQueryString();
        return view('kasir.stocks.index', compact('stocks'));
    }
}
