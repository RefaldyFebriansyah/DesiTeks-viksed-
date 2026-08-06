<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['fabric','user']);

        if ($request->filled('search')) {
            $query->whereHas('fabric', fn($q) => $q->where('nama_kain','like','%'.$request->search.'%'));
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $movements = $query->orderBy('created_at','desc')->paginate(10)->withQueryString();
        return view('admin.stock-movements.index', compact('movements'));
    }
}
