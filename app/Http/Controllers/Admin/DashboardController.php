<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Fabric;
use App\Models\IncomingGood;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Summary cards
        $totalJenisKain     = Fabric::where('status', 'aktif')->count();
        $totalStokRol       = Stock::sum('stok_rol');
        $totalStokMeter     = (float) (Stock::join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->selectRaw('SUM(stocks.stok_meter + (stocks.stok_rol * COALESCE(fabrics.meter_per_rol, 50))) as total')
            ->value('total') ?? 0);
        $barangMasukHariIni = IncomingGood::whereDate('tanggal', $today)->count();
        $transaksiHariIni   = Sale::whereDate('created_at', $today)->where('status', 'berhasil')->count();
        $pendapatanHariIni  = Sale::whereDate('created_at', $today)->where('status', 'berhasil')->sum('total');

        // Transaksi terbaru
        $transaksiTerbaru = Sale::with(['user', 'payment'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Stok menipis / habis (stok_rol <= 5)
        $stokMenipis = Stock::with('fabric.category')
            ->join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->where('stocks.stok_rol', '<=', 5)
            ->select('stocks.*')
            ->orderBy('stocks.stok_rol', 'ASC')
            ->orderBy('stocks.stok_meter', 'ASC')
            ->limit(5)
            ->get();

        // Total count of low/out stock
        $jumlahStokMenipis = Stock::join('fabrics', 'stocks.fabric_id', '=', 'fabrics.id')
            ->where('fabrics.status', 'aktif')
            ->where('stocks.stok_rol', '<=', 5)
            ->count();

        // Aktivitas terbaru
        $aktivitasTerbaru = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Kain terlaris berdasarkan total omset
        $kainTerlaris = SaleDetail::with(['fabric.category'])
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'berhasil')
            ->selectRaw('fabric_id, SUM(subtotal) as total_omset, 
                         SUM(CASE WHEN satuan = "meter" THEN jumlah ELSE 0 END) as total_meter,
                         SUM(CASE WHEN satuan = "rol" THEN jumlah ELSE 0 END) as total_rol')
            ->groupBy('fabric_id')
            ->orderByDesc('total_omset')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalJenisKain', 'totalStokRol', 'totalStokMeter',
            'barangMasukHariIni', 'transaksiHariIni', 'pendapatanHariIni',
            'transaksiTerbaru', 'stokMenipis', 'jumlahStokMenipis', 'aktivitasTerbaru', 'kainTerlaris'
        ));
    }

    public function getChartData(Request $request)
    {
        $period = $request->get('period', 'minggu_ini');
        $labels = [];
        $totals = [];
        $totalsIncoming = [];
        
        $startDate = Carbon::today();
        $endDate   = Carbon::now();

        if ($period === 'hari_ini') {
            $startDate = Carbon::today()->startOfDay();
            $endDate   = Carbon::today()->endOfDay();

            for ($h = 0; $h < 24; $h += 2) {
                $startHour = Carbon::today()->setHour($h)->setMinute(0)->setSecond(0);
                $endHour   = Carbon::today()->setHour($h + 1)->setMinute(59)->setSecond(59);

                $labels[] = sprintf('%02d:00', $h);
                $totals[] = (float) Sale::whereBetween('created_at', [$startHour, $endHour])
                    ->where('status', 'berhasil')->sum('total');
                $totalsIncoming[] = (float) IncomingGood::whereBetween('created_at', [$startHour, $endHour])
                    ->sum('total_pembelian');
            }

        } elseif ($period === 'bulan_ini') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate   = Carbon::now()->endOfMonth();
            $daysInMonth = Carbon::now()->daysInMonth;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = Carbon::now()->setDate(Carbon::now()->year, Carbon::now()->month, $d);
                $labels[] = $d . ' ' . $date->translatedFormat('M');
                $totals[] = (float) Sale::whereDate('created_at', $date)
                    ->where('status', 'berhasil')->sum('total');
                $totalsIncoming[] = (float) IncomingGood::whereDate('tanggal', $date)
                    ->sum('total_pembelian');
            }

        } elseif ($period === 'tahun_ini') {
            $startDate = Carbon::now()->startOfYear();
            $endDate   = Carbon::now()->endOfYear();

            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::now()->setDate(Carbon::now()->year, $m, 1);
                $labels[] = $date->translatedFormat('F');
                $totals[] = (float) Sale::whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', $m)->where('status', 'berhasil')->sum('total');
                $totalsIncoming[] = (float) IncomingGood::whereYear('tanggal', Carbon::now()->year)
                    ->whereMonth('tanggal', $m)->sum('total_pembelian');
            }

        } else {
            // default: minggu_ini (7 Hari Terakhir)
            $startDate = Carbon::today()->subDays(6)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->translatedFormat('D, d M');
                $totals[] = (float) Sale::whereDate('created_at', $date)
                    ->where('status', 'berhasil')->sum('total');
                $totalsIncoming[] = (float) IncomingGood::whereDate('tanggal', $date)
                    ->sum('total_pembelian');
            }
        }

        // Summary metrics for selected period
        $salesQuery = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'berhasil');

        $totalPenjualan = $salesQuery->sum('total');
        $totalTransaksi = $salesQuery->count();

        $saleIds = $salesQuery->pluck('id');
        $totalMeter = SaleDetail::whereIn('sale_id', $saleIds)
            ->where('satuan', 'meter')->sum('jumlah');
            
        $totalRol = SaleDetail::whereIn('sale_id', $saleIds)
            ->where('satuan', 'rol')->sum('jumlah');

        // Data Grafik Batang (Top 5 Kain Terlaris Omset)
        $topKain = SaleDetail::with('fabric')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'berhasil')
            ->selectRaw('fabric_id, SUM(subtotal) as total_omset')
            ->groupBy('fabric_id')
            ->orderByDesc('total_omset')
            ->limit(5)
            ->get();

        $barLabels = [];
        $barData   = [];
        foreach ($topKain as $k) {
            $barLabels[] = $k->fabric?->nama_kain ?? 'Kain';
            $barData[]   = (float) $k->total_omset;
        }

        return response()->json([
            'labels'          => $labels,
            'totals'          => $totals,
            'totals_incoming' => $totalsIncoming,
            'bar_labels'      => $barLabels,
            'bar_data'        => $barData,
            'summary' => [
                'total_penjualan' => 'Rp ' . number_format($totalPenjualan, 0, ',', '.'),
                'total_transaksi' => number_format($totalTransaksi) . ' transaksi',
                'total_meter'     => number_format($totalMeter, 1) . ' m',
                'total_rol'       => number_format($totalRol) . ' rol',
            ]
        ]);
    }
}
