<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function($qb) use ($q) {
                $qb->where('nama', 'like', "%$q%")
                   ->orWhere('kode_pelanggan', 'like', "%$q%")
                   ->orWhere('telepon', 'like', "%$q%");
            });
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $customers = $query->orderBy('nama')->paginate(10)->withQueryString();
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'           => 'required|string|max:100',
            'telepon'        => 'nullable|string|max:20',
            'alamat'         => 'nullable|string|max:500',
            'tipe'           => 'required|in:eceran,grosir,member',
            'diskon_member'  => 'required|numeric|min:0|max:100',
        ]);

        // Auto-generate kode_pelanggan jika kosong
        $kode = trim($request->kode_pelanggan ?? '');
        if (!$kode) {
            $last = Customer::orderBy('id', 'desc')->first();
            $seq  = $last ? $last->id + 1 : 1;
            $kode = 'CUST-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
        }

        $customer = Customer::create([
            'kode_pelanggan' => $kode,
            'nama'           => $request->nama,
            'telepon'        => $request->telepon,
            'alamat'         => $request->alamat,
            'tipe'           => $request->tipe,
            'diskon_member'  => $request->tipe === 'member' ? $request->diskon_member : 0,
        ]);

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menambahkan pelanggan baru: {$customer->nama} ({$customer->kode_pelanggan})",
            'model'     => 'Customer',
            'model_id'  => $customer->id,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'nama'           => 'required|string|max:100',
            'telepon'        => 'nullable|string|max:20',
            'alamat'         => 'nullable|string|max:500',
            'tipe'           => 'required|in:eceran,grosir,member',
            'diskon_member'  => 'required|numeric|min:0|max:100',
        ]);

        $customer->update([
            'nama'          => $request->nama,
            'telepon'       => $request->telepon,
            'alamat'        => $request->alamat,
            'tipe'          => $request->tipe,
            'diskon_member' => $request->tipe === 'member' ? $request->diskon_member : 0,
        ]);

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Mengubah data pelanggan: {$customer->nama} ({$customer->kode_pelanggan})",
            'model'     => 'Customer',
            'model_id'  => $customer->id,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $nama = $customer->nama;
        $kode = $customer->kode_pelanggan;
        
        $customer->delete();

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menghapus pelanggan: {$nama} ({$kode})",
            'model'     => 'Customer',
            'model_id'  => 0,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
