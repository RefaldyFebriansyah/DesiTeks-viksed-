<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'hari_ini');
        [$dari, $sampai] = $this->getPeriode($periode, $request);

        // Laporan penjualan
        $totalPendapatan  = Sale::whereBetween('created_at', [$dari, $sampai])->where('status','berhasil')->sum('total');
        $totalTransaksi   = Sale::whereBetween('created_at', [$dari, $sampai])->where('status','berhasil')->count();
        $totalKainTerjual = SaleDetail::whereHas('sale', fn($q) => $q->whereBetween('created_at', [$dari, $sampai])->where('status','berhasil'))->sum('jumlah');

        // Penjualan per kain
        $penjualanPerKain = SaleDetail::with('fabric')
            ->whereHas('sale', fn($q) => $q->whereBetween('created_at', [$dari, $sampai])->where('status','berhasil'))
            ->selectRaw('fabric_id, satuan, SUM(jumlah) as total_jumlah, SUM(subtotal) as total_subtotal')
            ->groupBy('fabric_id','satuan')
            ->orderByDesc('total_subtotal')
            ->get();

        // Stok saat ini
        $stokReport = Stock::with('fabric.category')
            ->join('fabrics','stocks.fabric_id','=','fabrics.id')
            ->where('fabrics.status','aktif')
            ->select('stocks.*')
            ->orderBy('fabrics.kode_kain')
            ->get();

        return view('admin.reports.index', compact(
            'totalPendapatan','totalTransaksi','totalKainTerjual',
            'penjualanPerKain','stokReport','periode','dari','sampai'
        ));
    }

    public function exportCsv(Request $request)
    {
        $periode = $request->get('periode', 'hari_ini');
        [$dari, $sampai] = $this->getPeriode($periode, $request);

        // Ambil penjualan per kain
        $penjualanPerKain = SaleDetail::with('fabric')
            ->whereHas('sale', fn($q) => $q->whereBetween('created_at', [$dari, $sampai])->where('status','berhasil'))
            ->selectRaw('fabric_id, satuan, SUM(jumlah) as total_jumlah, SUM(subtotal) as total_subtotal')
            ->groupBy('fabric_id','satuan')
            ->orderByDesc('total_subtotal')
            ->get();

        $filename = "laporan-penjualan-mitraseratbuana-{$periode}-" . date('Ymd') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($penjualanPerKain, $dari, $sampai, $periode) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan UTF-8 BOM untuk kompatibilitas Microsoft Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ["LAPORAN PENJUALAN MITRASERATBUANA"]);
            fputcsv($file, ["Periode Laporan", ucfirst(str_replace('_', ' ', $periode))]);
            fputcsv($file, ["Rentang Tanggal", $dari->format('d-m-Y') . " s/d " . $sampai->format('d-m-Y')]);
            fputcsv($file, []);
            
            fputcsv($file, ["Kode Kain", "Nama Kain", "Satuan", "Total Kuantitas Terjual", "Total Omset (Rp)"]);

            foreach ($penjualanPerKain as $item) {
                fputcsv($file, [
                    $item->fabric->kode_kain ?? '-',
                    $item->fabric->nama_kain ?? 'Kain Dihapus',
                    ucfirst($item->satuan),
                    $item->total_jumlah,
                    round($item->total_subtotal)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getPeriode(string $periode, Request $request): array

    {
        return match($periode) {
            'minggu_ini'  => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'bulan_ini'   => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'custom'      => [
                Carbon::parse($request->dari)->startOfDay(),
                Carbon::parse($request->sampai)->endOfDay(),
            ],
            default       => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()], // hari_ini
        };
    }
}
