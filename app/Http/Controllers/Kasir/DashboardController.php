<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $user  = auth()->user();

        $pendapatanHariIni  = Sale::whereDate('created_at', $today)->where('status','berhasil')->sum('total');
        $transaksiHariIni   = Sale::whereDate('created_at', $today)->where('status','berhasil')->count();
        $kainTerjualHariIni = SaleDetail::whereHas('sale', fn($q) =>
            $q->whereDate('created_at', $today)->where('status','berhasil')
        )->sum('jumlah');

        $transaksiTerbaru = Sale::with(['payment'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at','desc')
            ->limit(8)->get();

        $stokKain = Stock::with('fabric.category')
            ->join('fabrics','stocks.fabric_id','=','fabrics.id')
            ->where('fabrics.status','aktif')
            ->whereRaw('stocks.stok_meter > 0')
            ->select('stocks.*')
            ->orderBy('fabrics.nama_kain')
            ->limit(10)->get();

        return view('kasir.dashboard', compact(
            'pendapatanHariIni','transaksiHariIni','kainTerjualHariIni',
            'transaksiTerbaru','stokKain'
        ));
    }
}
