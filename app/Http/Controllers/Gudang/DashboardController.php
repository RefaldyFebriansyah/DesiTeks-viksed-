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

    public function getChartData(\Illuminate\Http\Request $request)
    {
        $period = $request->get('period', 'minggu_ini');
        $labels = [];
        $rolMasuk = [];
        $fakturMasuk = [];

        if ($period === 'hari_ini') {
            for ($h = 0; $h < 24; $h += 2) {
                $startHour = Carbon::today()->setHour($h)->setMinute(0)->setSecond(0);
                $endHour   = Carbon::today()->setHour($h + 1)->setMinute(59)->setSecond(59);

                $labels[] = sprintf('%02d:00', $h);
                $rolMasuk[] = (int) IncomingGood::whereBetween('created_at', [$startHour, $endHour])->sum('total_rol');
                $fakturMasuk[] = (int) IncomingGood::whereBetween('created_at', [$startHour, $endHour])->count();
            }
        } elseif ($period === 'bulan_ini') {
            $daysInMonth = Carbon::now()->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = Carbon::now()->setDate(Carbon::now()->year, Carbon::now()->month, $d);
                $labels[] = $d . ' ' . $date->translatedFormat('M');
                $rolMasuk[] = (int) IncomingGood::whereDate('tanggal', $date)->sum('total_rol');
                $fakturMasuk[] = (int) IncomingGood::whereDate('tanggal', $date)->count();
            }
        } elseif ($period === 'tahun_ini') {
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::now()->setDate(Carbon::now()->year, $m, 1);
                $labels[] = $date->translatedFormat('F');
                $rolMasuk[] = (int) IncomingGood::whereYear('tanggal', Carbon::now()->year)
                    ->whereMonth('tanggal', $m)->sum('total_rol');
                $fakturMasuk[] = (int) IncomingGood::whereYear('tanggal', Carbon::now()->year)
                    ->whereMonth('tanggal', $m)->count();
            }
        } else {
            // default: minggu_ini (7 hari)
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->translatedFormat('D, d M');
                $rolMasuk[] = (int) IncomingGood::whereDate('tanggal', $date)->sum('total_rol');
                $fakturMasuk[] = (int) IncomingGood::whereDate('tanggal', $date)->count();
            }
        }

        // Data Grafik Batang: Stok Real per Jenis Kain (Maksimal 25 Rol per Kain)
        $stokPerKain = Fabric::where('status', 'aktif')
            ->join('stocks', 'fabrics.id', '=', 'stocks.fabric_id')
            ->select('fabrics.nama_kain', 'stocks.stok_rol')
            ->orderBy('fabrics.nama_kain', 'asc')
            ->get();

        $barLabels = [];
        $barData   = [];
        foreach ($stokPerKain as $item) {
            $barLabels[] = $item->nama_kain;
            $barData[]   = (int) $item->stok_rol;
        }

        return response()->json([
            'labels'       => $labels,
            'rol_masuk'    => $rolMasuk,
            'faktur_masuk' => $fakturMasuk,
            'bar_labels'   => $barLabels,
            'bar_data'     => $barData,
        ]);
    }
}
