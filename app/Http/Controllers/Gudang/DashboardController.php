<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Fabric;
use App\Models\IncomingGood;
use App\Models\Stock;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalJenisKain       = Fabric::where('status','aktif')->count();
        $totalStokRol         = Stock::sum('stok_rol');
        $totalStokMeter       = Stock::sum('stok_meter');
        $barangMasukHariIni   = IncomingGood::whereDate('tanggal', $today)->count();

        $stokMenipis = Stock::with('fabric.category')
            ->join('fabrics','stocks.fabric_id','=','fabrics.id')
            ->where('fabrics.status','aktif')
            ->whereRaw('stocks.stok_meter <= fabrics.stok_minimum')
            ->select('stocks.*')
            ->limit(5)->get();

        $barangMasukTerbaru = IncomingGood::with(['supplier','user'])
            ->orderBy('created_at','desc')->limit(8)->get();

        return view('gudang.dashboard', compact(
            'totalJenisKain','totalStokRol','totalStokMeter',
            'barangMasukHariIni','stokMenipis','barangMasukTerbaru'
        ));
    }
}
