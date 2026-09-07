<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleDetail;
use Carbon\Carbon;

class IncomeController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $pendapatanHariIni  = Sale::whereDate('created_at', $today)->where('status','berhasil')->sum('total');
        $transaksiHariIni   = Sale::whereDate('created_at', $today)->where('status','berhasil')->count();

        $detailsToday = SaleDetail::with('fabric')->whereHas('sale', fn($q) =>
            $q->whereDate('created_at', $today)->where('status','berhasil')
        )->get();

        $totalMeterTerjual = 0.0;
        $totalRolTerjual   = 0;
        $totalVolumeMeter  = 0.0;

        foreach ($detailsToday as $d) {
            if ($d->satuan === 'rol') {
                $totalRolTerjual += (int) $d->jumlah;
                $mPerRol = (float) ($d->fabric?->meter_per_rol > 0 ? $d->fabric->meter_per_rol : 50);
                $totalVolumeMeter += ($d->jumlah * $mPerRol);
            } else {
                $totalMeterTerjual += (float) $d->jumlah;
                $totalVolumeMeter += (float) $d->jumlah;
            }
        }

        $kainTerjualHariIni = $totalVolumeMeter;

        $transaksi = Sale::with(['payment','details.fabric'])
            ->whereDate('created_at', $today)
            ->where('status','berhasil')
            ->orderBy('created_at','desc')
            ->get();

        return view('kasir.income', compact(
            'pendapatanHariIni',
            'transaksiHariIni',
            'totalVolumeMeter',
            'totalMeterTerjual',
            'totalRolTerjual',
            'kainTerjualHariIni',
            'transaksi'
        ));
    }
}
