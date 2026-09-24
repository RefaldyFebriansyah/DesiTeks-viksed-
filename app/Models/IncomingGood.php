<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class IncomingGood extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'nomor_faktur',
        'supplier_id',
        'user_id',
        'branch_id',
        'tanggal',
        'catatan',
        'foto_lampiran',
        'total_pembelian',
        'total_rol',
        'total_meter',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'total_pembelian'  => 'decimal:2',
        'total_meter'      => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(IncomingGoodsDetail::class, 'incoming_good_id');
    }

    public function deliveryOrder()
    {
        return $this->hasOne(DeliveryOrder::class, 'incoming_good_id');
    }

    /**
     * Dapatkan atau buat otomatis DeliveryOrder (Surat Jalan) terkait faktur barang masuk ini.
     */
    public function getOrCreateDeliveryOrder(): DeliveryOrder
    {
        if ($this->deliveryOrder) {
            return $this->deliveryOrder;
        }

        $existing = DeliveryOrder::where('incoming_good_id', $this->id)
            ->orWhere('nomor_surat_jalan', $this->nomor_faktur)
            ->first();

        if ($existing) {
            if (!$existing->incoming_good_id) {
                $existing->update(['incoming_good_id' => $this->id]);
            }
            return $existing;
        }

        $do = DeliveryOrder::create([
            'nomor_surat_jalan'         => $this->nomor_faktur,
            'supplier_id'               => $this->supplier_id,
            'user_id'                   => $this->user_id,
            'branch_id'                 => $this->branch_id,
            'tanggal_kirim'             => $this->tanggal,
            'catatan'                   => $this->catatan,
            'foto_surat_jalan'          => $this->foto_lampiran,
            'status'                    => 'diterima',
            'approved_by_admin_user_id' => $this->user_id,
            'approved_at'               => $this->created_at ?? now(),
            'shipped_at'                => $this->created_at ?? now(),
            'received_by_user_id'       => $this->user_id,
            'received_at'               => $this->created_at ?? now(),
            'incoming_good_id'          => $this->id,
            'total_rol'                 => $this->total_rol,
            'total_meter'               => $this->total_meter,
            'total_nominal'             => $this->total_pembelian,
        ]);

        foreach ($this->details as $detail) {
            DeliveryOrderItem::create([
                'delivery_order_id' => $do->id,
                'fabric_id'         => $detail->fabric_id,
                'nama_kain'         => $detail->fabric?->nama_kain ?? 'Kain',
                'jenis_kain'        => $detail->fabric?->jenis_kain ?? '-',
                'warna'             => $detail->fabric?->warna ?? '-',
                'jumlah_rol'        => $detail->jumlah_rol,
                'jumlah_meter'      => $detail->jumlah_meter,
                'harga_satuan'      => $detail->harga_beli,
                'subtotal'          => $detail->subtotal,
            ]);
        }

        return $do;
    }

    /**
     * Generate nomor faktur / surat jalan otomatis sesuai inisial perusahaan supplier
     * dan jumlah transaksi masuk dari perusahaan tersebut ke MitraSeratBuana.
     */
    public static function generateNomorFaktur(?string $companyName = null): string
    {
        $prefix = 'SUP';
        $count = 0;

        if ($companyName) {
            $trimmed = trim($companyName);
            
            // Cari supplier untuk menghitung riwayat transaksi ke MitraSeratBuana
            $supplier = Supplier::where('nama_supplier', $trimmed)
                ->orWhereRaw('LOWER(nama_supplier) = ?', [strtolower($trimmed)])
                ->first();

            if ($supplier) {
                $count = $supplier->incomingGoods()->count();
            }

            // Ekstraksi inisial nama PT / Supplier
            $cleaned = preg_replace('/^(pt\.?|cv\.?|ud\.?|fa\.?|toko|tb\.?)\s+/i', '', $trimmed);
            $words = preg_split('/[\s\-_.]+/', trim($cleaned));
            $letters = '';
            foreach ($words as $w) {
                if (strlen($w) > 0) {
                    $letters .= strtoupper(substr($w, 0, 1));
                }
            }
            if (strlen($letters) >= 2) {
                $prefix = substr($letters, 0, 4);
            } elseif (strlen($cleaned) >= 3) {
                $prefix = strtoupper(substr($cleaned, 0, 3));
            }
        }

        $year = now()->format('Y');
        $seq = $count + 1; // Transaksi ke-(count + 1) dari supplier ini ke MitraSeratBuana

        do {
            $numStr = str_pad($seq, 4, '0', STR_PAD_LEFT);
            $gen = "SJ-{$year}/{$prefix}/{$numStr}";
            $seq++;
        } while (self::where('nomor_faktur', $gen)->exists());

        return $gen;
    }
}
