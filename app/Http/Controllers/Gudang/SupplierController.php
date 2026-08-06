<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\AuditLog;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('incomingGoods')->orderBy('nama_supplier')->paginate(10);
        return view('gudang.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('gudang.suppliers.create');
    }

    public function store(SupplierRequest $request)
    {
        $last   = Supplier::orderBy('id', 'desc')->first();
        $seq    = $last ? ((int) substr($last->kode_supplier, 3)) + 1 : 1;
        $kode   = 'SUP' . str_pad($seq, 3, '0', STR_PAD_LEFT);

        $supplier = Supplier::create(array_merge($request->validated(), ['kode_supplier' => $kode]));

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menambah supplier (Gudang): {$supplier->nama_supplier}",
            'model'     => 'Supplier',
            'model_id'  => $supplier->id,
        ]);

        return redirect()->route('gudang.suppliers.index')
            ->with('success', "Supplier {$supplier->nama_supplier} berhasil ditambahkan.");
    }

    public function edit(Supplier $supplier)
    {
        return view('gudang.suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Mengubah supplier (Gudang): {$supplier->nama_supplier}",
        ]);

        return redirect()->route('gudang.suppliers.index')
            ->with('success', "Data supplier berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->incomingGoods()->count() > 0) {
            return back()->with('error', 'Supplier tidak dapat dihapus karena memiliki riwayat barang masuk.');
        }

        $nama = $supplier->nama_supplier;
        $supplier->delete();

        return redirect()->route('gudang.suppliers.index')
            ->with('success', "Supplier {$nama} berhasil dihapus.");
    }
}
