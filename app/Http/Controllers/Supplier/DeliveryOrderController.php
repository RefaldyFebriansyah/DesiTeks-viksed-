<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Fabric;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryOrderController extends Controller
{
    private function getSupplier(): Supplier
    {
        $user = Auth::user();
        if ($user->supplier) {
            return $user->supplier;
        }

        // Jika belum tertaut ke supplier id, cari atau buat berdasarkan data user
        $supplier = Supplier::where('nama_supplier', $user->name)->first();
        if (!$supplier) {
            $last = Supplier::orderBy('id', 'desc')->first();
            $seq  = $last ? ((int) substr($last->kode_supplier, 3)) + 1 : 1;
            $kode = 'SUP' . str_pad($seq, 3, '0', STR_PAD_LEFT);

            $supplier = Supplier::create([
                'kode_supplier' => $kode,
                'nama_supplier' => $user->name,
                'email'         => $user->email,
            ]);
        }

        $user->update(['supplier_id' => $supplier->id]);
        return $supplier;
    }

    public function index(Request $request)
    {
        $supplier = $this->getSupplier();
        $status = $request->input('status', 'all');
        $search = trim($request->input('q', ''));

        $query = DeliveryOrder::where('supplier_id', $supplier->id)
            ->with(['branch', 'receivedBy']);

        if ($status !== 'all' && in_array($status, ['menunggu_approval', 'disetujui_admin', 'dalam_perjalanan', 'dikirim', 'diterima', 'ditolak'])) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('nama_supir', 'like', "%{$search}%")
                  ->orWhere('plat_nomor', 'like', "%{$search}%")
                  ->orWhere('ekspedisi', 'like', "%{$search}%");
            });
        }

        $deliveryOrders = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all'               => DeliveryOrder::where('supplier_id', $supplier->id)->count(),
            'menunggu_approval' => DeliveryOrder::where('supplier_id', $supplier->id)->where('status', 'menunggu_approval')->count(),
            'disetujui_admin'   => DeliveryOrder::where('supplier_id', $supplier->id)->whereIn('status', ['disetujui_admin', 'dikirim'])->count(),
            'dalam_perjalanan'  => DeliveryOrder::where('supplier_id', $supplier->id)->where('status', 'dalam_perjalanan')->count(),
            'diterima'          => DeliveryOrder::where('supplier_id', $supplier->id)->where('status', 'diterima')->count(),
            'ditolak'           => DeliveryOrder::where('supplier_id', $supplier->id)->where('status', 'ditolak')->count(),
        ];

        return view('supplier.delivery-orders.index', compact('deliveryOrders', 'status', 'search', 'counts', 'supplier'));
    }

    public function create()
    {
        $supplier = $this->getSupplier();
        $branches = Branch::all();
        $fabrics  = Fabric::where('status', 'aktif')->orderBy('nama_kain')->get();
        $autoNomor = DeliveryOrder::generateNomorSuratJalan($supplier);

        return view('supplier.delivery-orders.create', compact('supplier', 'branches', 'fabrics', 'autoNomor'));
    }

    public function store(Request $request)
    {
        $supplier = $this->getSupplier();

        $request->validate([
            'tanggal_kirim'      => 'required|date',
            'branch_id'          => 'required|exists:branches,id',
            'nama_supir'         => 'nullable|string|max:100',
            'plat_nomor'         => 'nullable|string|max:50',
            'ekspedisi'          => 'nullable|string|max:100',
            'catatan'            => 'nullable|string|max:1000',
            'foto_surat_jalan'   => 'nullable|image|max:4096',
            'items'              => 'required|array|min:1',
            'items.*.nama_kain'   => 'required|string|max:150',
            'items.*.jumlah_rol'  => 'required|integer|min:0',
            'items.*.jumlah_meter'=> 'required|numeric|min:0.1',
            'items.*.harga_satuan'=> 'nullable|numeric|min:0',
        ], [
            'branch_id.required'           => 'Pilih gudang/cabang tujuan pengiriman.',
            'items.required'               => 'Daftar item kain wajib diisi minimal 1 item.',
            'items.*.nama_kain.required'   => 'Nama kain pada baris item wajib diisi.',
            'items.*.jumlah_meter.required'=> 'Jumlah meter kain wajib diisi.',
        ]);

        $deliveryOrder = DB::transaction(function () use ($request, $supplier) {
            $fotoPath = null;
            if ($request->hasFile('foto_surat_jalan')) {
                $fotoPath = $request->file('foto_surat_jalan')->store('delivery_orders', 'public');
            }

            $nomorSuratJalan = trim($request->input('nomor_surat_jalan')) ?: DeliveryOrder::generateNomorSuratJalan($supplier);

            $totalRol     = 0;
            $totalMeter   = 0;
            $totalNominal = 0;

            foreach ($request->items as $item) {
                $rol   = (int) ($item['jumlah_rol'] ?? 0);
                $meter = (float) ($item['jumlah_meter'] ?? 0);
                $harga = (float) ($item['harga_satuan'] ?? 0);

                $totalRol     += $rol;
                $totalMeter   += $meter;
                $totalNominal += ($meter * $harga);
            }

            $order = DeliveryOrder::create([
                'nomor_surat_jalan' => $nomorSuratJalan,
                'supplier_id'       => $supplier->id,
                'user_id'           => Auth::id(),
                'branch_id'         => $request->branch_id,
                'tanggal_kirim'     => $request->tanggal_kirim,
                'nama_supir'        => $request->nama_supir,
                'plat_nomor'        => $request->plat_nomor,
                'ekspedisi'         => $request->ekspedisi,
                'catatan'           => $request->catatan,
                'foto_surat_jalan'  => $fotoPath,
                'status'            => 'menunggu_approval',
                'total_rol'         => $totalRol,
                'total_meter'       => $totalMeter,
                'total_nominal'     => $totalNominal,
            ]);

            foreach ($request->items as $item) {
                $meter = (float) ($item['jumlah_meter'] ?? 0);
                $harga = (float) ($item['harga_satuan'] ?? 0);
                $subtotal = $meter * $harga;

                DeliveryOrderItem::create([
                    'delivery_order_id' => $order->id,
                    'fabric_id'         => !empty($item['fabric_id']) && is_numeric($item['fabric_id']) ? $item['fabric_id'] : null,
                    'nama_kain'         => trim($item['nama_kain']),
                    'jenis_kain'        => $item['jenis_kain'] ?? null,
                    'warna'             => $item['warna'] ?? null,
                    'jumlah_rol'        => (int) ($item['jumlah_rol'] ?? 0),
                    'jumlah_meter'      => $meter,
                    'harga_satuan'      => $harga,
                    'subtotal'          => $subtotal,
                ]);
            }

            // Notifikasi ke Gudang & Admin
            AppNotification::create([
                'type'    => 'surat_jalan_masuk',
                'title'   => 'Surat Jalan Online Masuk',
                'message' => "Surat jalan {$order->nomor_surat_jalan} dari {$supplier->nama_supplier} sedang dalam perjalanan menuju gudang ({$totalRol} rol / " . number_format($totalMeter, 1) . " m).",
                'link'    => route('gudang.delivery-orders.show', $order->id),
            ]);

            AuditLog::create([
                'user_id'   => Auth::id(),
                'aktivitas' => "Supplier {$supplier->nama_supplier} membuat Surat Jalan Online: {$order->nomor_surat_jalan}",
                'model'     => 'DeliveryOrder',
                'model_id'  => $order->id,
            ]);

            return $order;
        });

        return redirect()->route('supplier.delivery-orders.show', $deliveryOrder->id)
            ->with('success', "Surat Jalan Online {$deliveryOrder->nomor_surat_jalan} berhasil dikirim ke Gudang! Anda dapat memantau status pemeriksaan secara langsung.");
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        $supplier = $this->getSupplier();
        if ($deliveryOrder->supplier_id !== $supplier->id) {
            abort(403, 'Anda tidak memiliki akses ke surat jalan ini.');
        }

        $deliveryOrder->load(['branch', 'user', 'receivedBy', 'items.fabric', 'incomingGood']);

        return view('supplier.delivery-orders.show', compact('deliveryOrder', 'supplier'));
    }

    public function print(DeliveryOrder $deliveryOrder)
    {
        $supplier = $this->getSupplier();
        if ($deliveryOrder->supplier_id !== $supplier->id) {
            abort(403);
        }

        $deliveryOrder->load(['branch', 'user', 'receivedBy', 'items.fabric']);

        return view('supplier.delivery-orders.print', compact('deliveryOrder', 'supplier'));
    }

    /**
     * Konfirmasi dari supplier bahwa barang sudah dalam perjalanan menuju gudang
     * (setelah di-ACC oleh Admin DesiTeks).
     */
    public function ship(Request $request, DeliveryOrder $deliveryOrder)
    {
        $supplier = $this->getSupplier();
        if ($deliveryOrder->supplier_id !== $supplier->id) {
            abort(403, 'Anda tidak memiliki akses ke surat jalan ini.');
        }

        if (!in_array($deliveryOrder->status, ['disetujui_admin', 'dikirim'])) {
            return back()->with('info', 'Surat jalan ini belum di-ACC Admin Toko atau sudah diproses lebih lanjut.');
        }

        $deliveryOrder->update([
            'status'     => 'dalam_perjalanan',
            'shipped_at' => now(),
        ]);

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Supplier {$supplier->nama_supplier} mengonfirmasi barang dalam perjalanan menuju gudang (Surat Jalan #{$deliveryOrder->nomor_surat_jalan})",
            'model'     => 'DeliveryOrder',
            'model_id'  => $deliveryOrder->id,
        ]);

        AppNotification::create([
            'type'    => 'surat_jalan_dalam_perjalanan',
            'title'   => 'Barang Dalam Perjalanan Ke Gudang',
            'message' => "Armada supplier {$supplier->nama_supplier} telah berangkat. Pengiriman Surat Jalan {$deliveryOrder->nomor_surat_jalan} kini sedang Dalam Perjalanan ke gudang.",
            'link'    => route('gudang.delivery-orders.show', $deliveryOrder->id),
        ]);

        return back()->with('success', "Konfirmasi berhasil! Status surat jalan {$deliveryOrder->nomor_surat_jalan} kini diperbarui menjadi 'Dalam Perjalanan'. Pihak gudang telah diberi tahu.");
    }
}
