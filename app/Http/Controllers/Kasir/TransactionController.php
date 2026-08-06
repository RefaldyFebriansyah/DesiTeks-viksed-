<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Sale::with(['payment'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at','desc')
            ->paginate(10);
        return view('kasir.transactions.index', compact('transactions'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['payment','details.fabric','user']);
        return view('kasir.transactions.show', compact('sale'));
    }
}
