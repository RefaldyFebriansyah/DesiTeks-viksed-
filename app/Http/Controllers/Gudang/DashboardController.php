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

        $totalJenisKain       = Fabric::where('status', 'aktif')->count();
        $totalStokRol         = Stock::sum('stok_rol');
        $totalStokMeter       = (float) (Stock::join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->selectRaw('SUM(stocks.stok_meter + (stocks.stok_rol * COALESCE(fabrics.meter_per_rol, 50))) as total')
            ->value('total') ?? 0);
        $barangMasukHariIni   = IncomingGood::whereDate('tanggal', $today)->count();

        $stokMenipis = Stock::with('fabric.category')
            ->join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->where('stocks.stok_rol', '<=', 5)
            ->select('stocks.*')
            ->orderBy('stocks.stok_rol', 'ASC')
            ->orderBy('stocks.stok_meter', 'ASC')
            ->limit(5)->get();

        $barangMasukTerbaru = IncomingGood::with(['supplier', 'user'])
            ->orderBy('created_at', 'desc')->limit(8)->get();

        return view('gudang.dashboard', compact(
            'totalJenisKain', 'totalStokRol', 'totalStokMeter',
            'barangMasukHariIni', 'stokMenipis', 'barangMasukTerbaru'
        ));
    }
}
