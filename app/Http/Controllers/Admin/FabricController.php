<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FabricRequest;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Fabric;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FabricController extends Controller
{
    public function index(Request $request)
    {
        $query = Fabric::with(['category', 'stock']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_kain', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_kain', 'like', '%' . $request->search . '%')
                  ->orWhere('warna', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $fabrics    = $query->orderBy('kode_kain')->paginate(10)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('admin.fabrics.index', compact('fabrics', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('admin.fabrics.create', compact('categories'));
    }

    public function store(FabricRequest $request)
    {
        // Otomatis buat kategori jika belum ada di database
        $category = Category::firstOrCreate([
            'nama_kategori' => trim($request->nama_kategori)
        ]);

        $data = $request->validated();
        unset($data['nama_kategori']);
        $data['category_id'] = $category->id;

        $fabric = Fabric::create($data);

        // Buat stok awal 0
        Stock::create([
            'fabric_id'  => $fabric->id,
            'stok_rol'   => 0,
            'stok_meter' => 0,
            'updated_at' => now(),
        ]);

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menambah data kain: {$fabric->nama_kain} ({$fabric->kode_kain})",
            'model'     => 'Fabric',
            'model_id'  => $fabric->id,
            'data_baru' => $fabric->toArray(),
        ]);

        return redirect()->route('admin.fabrics.index')
            ->with('success', "Kain {$fabric->nama_kain} berhasil ditambahkan.");
    }

    public function show(Fabric $fabric)
    {
        $fabric->load(['category', 'stock', 'stockMovements.user' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }]);
        return view('admin.fabrics.show', compact('fabric'));
    }

    public function edit(Fabric $fabric)
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('admin.fabrics.edit', compact('fabric', 'categories'));
    }

    public function update(FabricRequest $request, Fabric $fabric)
    {
        $category = Category::firstOrCreate([
            'nama_kategori' => trim($request->nama_kategori)
        ]);

        $dataLama = $fabric->toArray();
        $data = $request->validated();
        unset($data['nama_kategori']);
        $data['category_id'] = $category->id;

        $fabric->update($data);

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Mengubah data kain: {$fabric->nama_kain} ({$fabric->kode_kain})",
            'model'     => 'Fabric',
            'model_id'  => $fabric->id,
            'data_lama' => $dataLama,
            'data_baru' => $fabric->fresh()->toArray(),
        ]);

        return redirect()->route('admin.fabrics.index')
            ->with('success', "Data kain {$fabric->nama_kain} berhasil diperbarui.");
    }

    public function destroy(Fabric $fabric)
    {
        // Tidak bisa hapus jika ada stok
        $stock = $fabric->stock;
        if ($stock && ($stock->stok_rol > 0 || $stock->stok_meter > 0)) {
            return back()->with('error', "Kain {$fabric->nama_kain} tidak dapat dihapus karena masih memiliki stok.");
        }

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menghapus data kain: {$fabric->nama_kain} ({$fabric->kode_kain})",
            'model'     => 'Fabric',
            'model_id'  => $fabric->id,
            'data_lama' => $fabric->toArray(),
        ]);

        $namaKain = $fabric->nama_kain;
        $fabric->delete();

        return redirect()->route('admin.fabrics.index')
            ->with('success', "Kain {$namaKain} berhasil dihapus.");
    }
}
