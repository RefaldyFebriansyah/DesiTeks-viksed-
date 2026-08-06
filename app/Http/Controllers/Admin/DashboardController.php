<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Fabric;
use App\Models\IncomingGood;
use App\Models\Sale;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Summary cards
        $totalJenisKain   = Fabric::where('status', 'aktif')->count();
        $totalStokRol     = Stock::sum('stok_rol');
        $totalStokMeter   = Stock::sum('stok_meter');
        $barangMasukHariIni = IncomingGood::whereDate('tanggal', $today)->count();
        $transaksiHariIni   = Sale::whereDate('created_at', $today)->where('status', 'berhasil')->count();
        $pendapatanHariIni  = Sale::whereDate('created_at', $today)->where('status', 'berhasil')->sum('total');

        // Transaksi terbaru
        $transaksiTerbaru = Sale::with(['user', 'payment'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Stok menipis
        $stokMenipis = Stock::with('fabric.category')
            ->join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->whereRaw('stocks.stok_meter <= fabrics.stok_minimum')
            ->select('stocks.*')
            ->limit(5)
            ->get();

        // Aktivitas terbaru
        $aktivitasTerbaru = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Data grafik penjualan 7 hari
        $grafikData = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);
            $grafikData[] = [
                'label'  => $tanggal->translatedFormat('d M'),
                'total'  => (float) Sale::whereDate('created_at', $tanggal)
                                ->where('status', 'berhasil')
                                ->sum('total'),
            ];
        }

        return view('admin.dashboard', compact(
            'totalJenisKain', 'totalStokRol', 'totalStokMeter',
            'barangMasukHariIni', 'transaksiHariIni', 'pendapatanHariIni',
            'transaksiTerbaru', 'stokMenipis', 'aktivitasTerbaru', 'grafikData'
        ));
    }
}
