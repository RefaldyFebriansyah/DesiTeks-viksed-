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
        $kainTerjualHariIni = SaleDetail::whereHas('sale', fn($q) =>
            $q->whereDate('created_at', $today)->where('status','berhasil')
        )->sum('jumlah');

        $transaksi = Sale::with(['payment','details'])
            ->whereDate('created_at', $today)
            ->where('status','berhasil')
            ->orderBy('created_at','desc')
            ->get();

        return view('kasir.income', compact('pendapatanHariIni','transaksiHariIni','kainTerjualHariIni','transaksi'));
    }
}
