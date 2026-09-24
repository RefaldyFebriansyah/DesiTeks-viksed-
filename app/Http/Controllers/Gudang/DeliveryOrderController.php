<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DeliveryOrder;
use App\Models\Fabric;
use App\Models\IncomingGood;
use App\Models\IncomingGoodsDetail;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryOrderController extends Controller
{
    public function __construct(
        private StockService $stockService
    ) {}

    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = trim($request->input('q', ''));
        $branchId = session('active_branch_id') ?? Auth::user()->branch_id;

        $query = DeliveryOrder::with(['supplier', 'branch', 'receivedBy', 'approvedByAdmin', 'user']);

        // Jika staf cabang (bukan cabang 1 / pusat), filter sesuai cabangnya jika relevan
        if ($branchId && $branchId != 1) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        if ($status !== 'all' && in_array($status, ['menunggu_approval', 'disetujui_admin', 'dalam_perjalanan', 'dikirim', 'diterima', 'ditolak'])) {
            if ($status === 'disetujui_admin') {
                $query->whereIn('status', ['disetujui_admin', 'dikirim']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('nama_supir', 'like', "%{$search}%")
                  ->orWhere('plat_nomor', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('nama_supplier', 'like', "%{$search}%"));
            });
        }

        $deliveryOrders = $query->latest()->paginate(10)->withQueryString();

        $baseCountQuery = DeliveryOrder::query();
        if ($branchId && $branchId != 1) {
            $baseCountQuery->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        $counts = [
            'all'               => (clone $baseCountQuery)->count(),
            'menunggu_approval' => (clone $baseCountQuery)->where('status', 'menunggu_approval')->count(),
            'disetujui_admin'   => (clone $baseCountQuery)->whereIn('status', ['disetujui_admin', 'dikirim'])->count(),
            'dalam_perjalanan'  => (clone $baseCountQuery)->where('status', 'dalam_perjalanan')->count(),
            'diterima'          => (clone $baseCountQuery)->where('status', 'diterima')->count(),
            'ditolak'           => (clone $baseCountQuery)->where('status', 'ditolak')->count(),
        ];

        return view('gudang.delivery-orders.index', compact('deliveryOrders', 'status', 'search', 'counts'));
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['supplier', 'branch', 'user', 'approvedByAdmin', 'receivedBy', 'items.fabric', 'incomingGood.details']);

        return view('gudang.delivery-orders.show', compact('deliveryOrder'));
    }

    public function approveByAdmin(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status !== 'menunggu_approval') {
            return back()->with('info', 'Surat jalan ini sudah diproses atau disetujui sebelumnya.');
        }

        $deliveryOrder->update([
            'status'                    => 'disetujui_admin',
            'approved_by_admin_user_id' => Auth::id(),
            'approved_at'               => now(),
        ]);

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Admin Toko menyetujui (ACC) Surat Jalan Online {$deliveryOrder->nomor_surat_jalan} dari {$deliveryOrder->supplier->nama_supplier}",
            'model'     => 'DeliveryOrder',
            'model_id'  => $deliveryOrder->id,
        ]);

        AppNotification::create([
            'type'    => 'surat_jalan_disetujui_admin',
            'title'   => 'Surat Jalan Di-ACC Admin Toko',
            'message' => "Surat jalan {$deliveryOrder->nomor_surat_jalan} dari {$deliveryOrder->supplier->nama_supplier} telah di-ACC oleh Admin (" . Auth::user()->name . "). Status pengiriman kini Dalam Perjalanan menuju gudang.",
            'link'    => route('gudang.delivery-orders.show', $deliveryOrder->id),
        ]);

        return back()->with('success', "Surat Jalan {$deliveryOrder->nomor_surat_jalan} berhasil di-ACC oleh Admin Toko! Status pengiriman kini berubah menjadi 'Dalam Perjalanan'.");
    }

    public function accept(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status === 'menunggu_approval') {
            return back()->with('warning', 'Surat jalan ini belum di-ACC / disetujui oleh Admin Toko.');
        }

        if (in_array($deliveryOrder->status, ['disetujui_admin', 'dikirim'])) {
            return back()->with('warning', 'Barang belum dikirim oleh Supplier. Gudang hanya dapat menerima barang setelah Supplier mengonfirmasi keberangkatan ("Dalam Perjalanan").');
        }

        if ($deliveryOrder->status === 'diterima') {
            return back()->with('info', 'Surat jalan ini sudah pernah diterima sebelumnya.');
        }

        $request->validate([
            'catatan_gudang' => 'nullable|string|max:1000',
        ]);

        // Cek Batas Maksimum Stok Gudang
        foreach ($deliveryOrder->items as $item) {
            $fabric = $item->fabric_id ? Fabric::find($item->fabric_id) : Fabric::where('nama_kain', 'like', $item->nama_kain)->first();
            $existingRol = (int) ($fabric?->stock?->stok_rol ?? 0);
            $maxStok = (int) ($fabric?->stok_maksimum > 0 ? $fabric->stok_maksimum : 25);
            if ($maxStok > 0 && ($existingRol + $item->jumlah_rol) > $maxStok) {
                $sisaKuota = max(0, $maxStok - $existingRol);
                return back()->with('error', "Penerimaan ditolak: Kain '{$item->nama_kain}' akan melebihi kapasitas maksimum gudang ({$maxStok} rol). Stok gudang saat ini: {$existingRol} rol, barang masuk: {$item->jumlah_rol} rol (Sisa kuota: {$sisaKuota} rol).");
            }
        }

        DB::transaction(function () use ($request, $deliveryOrder) {
            $branchId = $deliveryOrder->branch_id ?? session('active_branch_id') ?? Auth::user()->branch_id ?? 1;

            // 1. Buat IncomingGood (Riwayat Barang Masuk)
            $incomingGood = IncomingGood::create([
                'nomor_faktur'    => $deliveryOrder->nomor_surat_jalan,
                'supplier_id'     => $deliveryOrder->supplier_id,
                'user_id'         => Auth::id(),
                'branch_id'       => $branchId,
                'tanggal'         => now()->toDateString(),
                'catatan'         => 'Penerimaan Surat Jalan Online: ' . ($request->catatan_gudang ?: ($deliveryOrder->catatan ?: 'Diterima oleh Gudang')),
                'foto_lampiran'   => $deliveryOrder->foto_surat_jalan,
                'total_pembelian' => $deliveryOrder->total_nominal,
                'total_rol'       => $deliveryOrder->total_rol,
                'total_meter'     => $deliveryOrder->total_meter,
            ]);

            // 2. Iterasi item kain, buat / cari kain, dan tambah stok
            foreach ($deliveryOrder->items as $item) {
                $fabric = null;

                if ($item->fabric_id) {
                    $fabric = Fabric::find($item->fabric_id);
                }

                // Jika kain belum ada di master kain, buatkan otomatis
                if (!$fabric) {
                    $category = Category::firstOrCreate(['nama_kategori' => 'Kain Supplier']);
                    $prefix   = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $item->nama_kain), 0, 3)) ?: 'KAI';
                    $lastFab  = Fabric::where('kode_kain', 'like', $prefix . '-%')->orderBy('id', 'desc')->first();
                    $seqFab   = $lastFab ? ((int) substr($lastFab->kode_kain, -3)) + 1 : 1;
                    $kodeKain = $prefix . '-' . str_pad($seqFab, 3, '0', STR_PAD_LEFT);

                    $fabric = Fabric::create([
                        'kode_kain'       => $kodeKain,
                        'nama_kain'       => $item->nama_kain,
                        'category_id'     => $category->id,
                        'jenis_kain'      => $item->jenis_kain,
                        'warna'           => $item->warna,
                        'harga_per_meter' => $item->harga_satuan > 0 ? ($item->harga_satuan * 1.25) : 35000,
                        'harga_per_rol'   => $item->harga_satuan > 0 ? ($item->harga_satuan * 50 * 1.2) : 1500000,
                        'meter_per_rol'   => 50.00,
                        'stok_minimum'    => 5,
                        'status'          => 'aktif',
                    ]);

                    Stock::create([
                        'fabric_id'  => $fabric->id,
                        'branch_id'  => $branchId,
                        'stok_rol'   => 0,
                        'stok_meter' => 0,
                        'updated_at' => now(),
                    ]);

                    $item->update(['fabric_id' => $fabric->id]);
                }

                // Simpan IncomingGoodsDetail
                IncomingGoodsDetail::create([
                    'incoming_good_id' => $incomingGood->id,
                    'fabric_id'        => $fabric->id,
                    'jumlah_rol'       => $item->jumlah_rol,
                    'jumlah_meter'     => $item->jumlah_meter,
                    'harga_beli'       => $item->harga_satuan,
                    'subtotal'         => $item->subtotal,
                ]);

                // Tambahkan Stok real-time
                $this->stockService->tambahStok($fabric, (int) $item->jumlah_rol, (float) $item->jumlah_meter, $incomingGood->id);
            }

            // 3. Update status DeliveryOrder
            $deliveryOrder->update([
                'status'              => 'diterima',
                'received_by_user_id' => Auth::id(),
                'received_at'         => now(),
                'catatan_gudang'      => $request->catatan_gudang,
                'incoming_good_id'    => $incomingGood->id,
            ]);

            // 4. Catat Audit Log & Notifikasi
            AuditLog::create([
                'user_id'   => Auth::id(),
                'aktivitas' => "Gudang menerima Surat Jalan Online {$deliveryOrder->nomor_surat_jalan} dari {$deliveryOrder->supplier->nama_supplier} (Faktur Masuk #{$incomingGood->id})",
                'model'     => 'DeliveryOrder',
                'model_id'  => $deliveryOrder->id,
            ]);

            AppNotification::create([
                'type'    => 'surat_jalan_diterima',
                'title'   => 'Surat Jalan Diterima & Stok Masuk',
                'message' => "Surat jalan {$deliveryOrder->nomor_surat_jalan} dari {$deliveryOrder->supplier->nama_supplier} telah DITERIMA oleh " . Auth::user()->name . ". Total {$deliveryOrder->total_rol} rol / " . number_format($deliveryOrder->total_meter, 1) . "m berhasil masuk ke stok gudang.",
                'link'    => route('gudang.incoming-goods.show', $incomingGood->id),
            ]);
        });

        return redirect()->route('gudang.delivery-orders.show', $deliveryOrder->id)
            ->with('success', "Surat Jalan {$deliveryOrder->nomor_surat_jalan} berhasil DITERIMA! Barang telah resmi masuk ke riwayat barang masuk dan stok gudang otomatis bertambah.");
    }

    public function reject(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status === 'diterima') {
            return back()->with('error', 'Surat jalan yang sudah diterima tidak dapat ditolak.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:1000',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan surat jalan wajib diisi agar supplier dapat melakukan konfirmasi.',
        ]);

        $deliveryOrder->update([
            'status'              => 'ditolak',
            'received_by_user_id' => Auth::id(),
            'received_at'         => now(),
            'catatan_gudang'      => $request->alasan_penolakan,
        ]);

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Gudang menolak Surat Jalan Online {$deliveryOrder->nomor_surat_jalan} dari {$deliveryOrder->supplier->nama_supplier}. Alasan: {$request->alasan_penolakan}",
            'model'     => 'DeliveryOrder',
            'model_id'  => $deliveryOrder->id,
        ]);

        AppNotification::create([
            'type'    => 'surat_jalan_ditolak',
            'title'   => 'Surat Jalan Ditolak Gudang',
            'message' => "Surat jalan {$deliveryOrder->nomor_surat_jalan} dari {$deliveryOrder->supplier->nama_supplier} DITOLAK. Alasan: {$request->alasan_penolakan}",
            'link'    => route('gudang.delivery-orders.show', $deliveryOrder->id),
        ]);

        return redirect()->route('gudang.delivery-orders.show', $deliveryOrder->id)
            ->with('warning', "Surat Jalan {$deliveryOrder->nomor_surat_jalan} ditandai sebagai DITOLAK. Catatan telah diteruskan ke pihak supplier.");
    }

    public function checkStatus(DeliveryOrder $deliveryOrder)
    {
        return response()->json([
            'id'          => $deliveryOrder->id,
            'status'      => $deliveryOrder->status,
            'updated_at'  => $deliveryOrder->updated_at->toIso8601String(),
            'approved_at' => $deliveryOrder->approved_at?->format('d M Y, H:i'),
            'shipped_at'  => $deliveryOrder->shipped_at?->format('d M Y, H:i'),
            'received_at' => $deliveryOrder->received_at?->format('d M Y, H:i'),
        ]);
    }

    public function checkAllStatus(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json([]);
        }

        $orders = DeliveryOrder::whereIn('id', $ids)->get(['id', 'status', 'updated_at']);

        $statuses = [];
        foreach ($orders as $order) {
            $statuses[$order->id] = [
                'status'     => $order->status,
                'updated_at' => $order->updated_at->toIso8601String(),
            ];
        }

        return response()->json($statuses);
    }

    public function print(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['branch', 'user', 'supplier', 'receivedBy', 'items.fabric']);
        return view('supplier.delivery-orders.print', compact('deliveryOrder'));
    }
}
