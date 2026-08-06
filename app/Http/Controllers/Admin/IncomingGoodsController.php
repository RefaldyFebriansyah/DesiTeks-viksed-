<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncomingGoodsRequest;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Fabric;
use App\Models\IncomingGood;
use App\Models\IncomingGoodsDetail;
use App\Models\Stock;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncomingGoodsController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = IncomingGood::with(['supplier', 'user']);

        if ($request->filled('search')) {
            $query->where('nomor_faktur', 'like', '%' . $request->search . '%')
                  ->orWhereHas('supplier', fn($q) => $q->where('nama_supplier', 'like', '%' . $request->search . '%'));
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $incomingGoods = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();
        return view('admin.incoming-goods.index', compact('incomingGoods'));
    }

    public function create()
    {
        $suppliers  = Supplier::orderBy('nama_supplier')->get();
        $categories = Category::orderBy('nama_kategori')->get();
        $fabrics    = Fabric::with('stock')->where('status', 'aktif')->orderBy('nama_kain')->get();
        return view('admin.incoming-goods.create', compact('suppliers', 'categories', 'fabrics'));
    }

    public function store(IncomingGoodsRequest $request)
    {
        DB::transaction(function () use ($request) {
            // 1. Otomatis buat / cari Supplier
            $supplierName = trim($request->nama_supplier);
            $supplier = Supplier::where('nama_supplier', $supplierName)->first();
            if (!$supplier) {
                $last = Supplier::orderBy('id', 'desc')->first();
                $seq  = $last ? ((int) substr($last->kode_supplier, 3)) + 1 : 1;
                $kode = 'SUP' . str_pad($seq, 3, '0', STR_PAD_LEFT);

                $supplier = Supplier::create([
                    'kode_supplier' => $kode,
                    'nama_supplier' => $supplierName,
                ]);
            }

            $totalRol   = 0;
            $totalMeter = 0;
            $totalBeli  = 0;

            foreach ($request->items as $item) {
                $totalRol   += $item['jumlah_rol'];
                $totalMeter += $item['jumlah_meter'];
                $totalBeli  += $item['jumlah_meter'] * $item['harga_beli'];
            }

            // 2. Buat record barang masuk
            $incomingGood = IncomingGood::create([
                'nomor_faktur'    => $request->nomor_faktur,
                'supplier_id'     => $supplier->id,
                'user_id'         => Auth::id(),
                'tanggal'         => $request->tanggal,
                'catatan'         => $request->catatan,
                'total_pembelian' => $totalBeli,
                'total_rol'       => $totalRol,
                'total_meter'     => $totalMeter,
            ]);

            // 3. Simpan detail, buat kain baru jika dipilih "new", & update stok
            foreach ($request->items as $item) {
                $subtotal = $item['jumlah_meter'] * $item['harga_beli'];

                if ($item['fabric_id'] === 'new') {
                    // Otomatis buat Kategori jika baru
                    $category = Category::firstOrCreate([
                        'nama_kategori' => trim($item['nama_kategori'])
                    ]);

                    // Auto-generate kode kain jika kosong
                    $kodeKain = trim($item['kode_kain'] ?? '');
                    if (!$kodeKain) {
                        $prefix   = strtoupper(substr($category->nama_kategori, 0, 3));
                        $lastFab  = Fabric::where('kode_kain', 'like', $prefix . '-%')->orderBy('id', 'desc')->first();
                        $seqFab   = $lastFab ? ((int) substr($lastFab->kode_kain, -3)) + 1 : 1;
                        $kodeKain = $prefix . '-' . str_pad($seqFab, 3, '0', STR_PAD_LEFT);
                    }

                    // Buat Kain Baru
                    $fabric = Fabric::create([
                        'kode_kain'       => $kodeKain,
                        'nama_kain'       => trim($item['nama_kain']),
                        'category_id'     => $category->id,
                        'jenis_kain'      => $item['jenis_kain'] ?? null,
                        'warna'           => $item['warna'] ?? null,
                        'harga_per_meter' => $item['harga_per_meter'] ?? 0,
                        'harga_per_rol'   => $item['harga_per_rol'] ?? 0,
                        'stok_minimum'    => 10,
                        'status'          => 'aktif',
                    ]);

                    // Inisialisasi Stok
                    Stock::create([
                        'fabric_id'  => $fabric->id,
                        'stok_rol'   => 0,
                        'stok_meter' => 0,
                        'updated_at' => now(),
                    ]);
                } else {
                    $fabric = Fabric::findOrFail($item['fabric_id']);
                }

                IncomingGoodsDetail::create([
                    'incoming_good_id' => $incomingGood->id,
                    'fabric_id'        => $fabric->id,
                    'jumlah_rol'       => $item['jumlah_rol'],
                    'jumlah_meter'     => $item['jumlah_meter'],
                    'harga_beli'       => $item['harga_beli'],
                    'subtotal'         => $subtotal,
                ]);

                $this->stockService->tambahStok($fabric, $item['jumlah_rol'], $item['jumlah_meter'], $incomingGood->id);
            }

            AuditLog::create([
                'user_id'   => Auth::id(),
                'aktivitas' => "Barang masuk: {$incomingGood->nomor_faktur} dari {$supplier->nama_supplier}",
                'model'     => 'IncomingGood',
                'model_id'  => $incomingGood->id,
            ]);
        });

        return redirect()->route('admin.incoming-goods.index')
            ->with('success', 'Barang masuk berhasil dicatat. Supplier, Kain, Kategori, dan Stok telah diperbarui otomatis!');
    }

    public function show(IncomingGood $incomingGood)
    {
        $incomingGood->load(['supplier', 'user', 'details.fabric.category']);
        return view('admin.incoming-goods.show', compact('incomingGood'));
    }
}
