<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'payment']);

        if ($request->filled('search')) {
            $query->where('nomor_transaksi', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'payment', 'details.fabric.category']);
        return view('admin.transactions.show', compact('sale'));
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status === 'dibatalkan') {
            return back()->with('error', 'Transaksi sudah dibatalkan.');
        }
        $sale->update(['status' => 'dibatalkan']);
        \App\Models\AuditLog::create([
            'user_id'   => auth()->id(),
            'aktivitas' => "Membatalkan transaksi: {$sale->nomor_transaksi}",
            'model'     => 'Sale',
            'model_id'  => $sale->id,
        ]);
        return back()->with('success', 'Transaksi berhasil dibatalkan.');
    }
}
