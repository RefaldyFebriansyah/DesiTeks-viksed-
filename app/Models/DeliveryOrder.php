<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $fillable = [
        'nomor_surat_jalan',
        'supplier_id',
        'user_id',
        'branch_id',
        'tanggal_kirim',
        'nama_supir',
        'plat_nomor',
        'ekspedisi',
        'catatan',
        'foto_surat_jalan',
        'status',
        'approved_by_admin_user_id',
        'approved_at',
        'shipped_at',
        'catatan_gudang',
        'received_by_user_id',
        'received_at',
        'incoming_good_id',
        'total_rol',
        'total_meter',
        'total_nominal',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'approved_at'   => 'datetime',
        'shipped_at'    => 'datetime',
        'received_at'   => 'datetime',
        'total_meter'   => 'decimal:2',
        'total_nominal' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function approvedByAdmin()
    {
        return $this->belongsTo(User::class, 'approved_by_admin_user_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function incomingGood()
    {
        return $this->belongsTo(IncomingGood::class);
    }

    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class, 'delivery_order_id');
    }

    /**
     * Generate nomor surat jalan online otomatis.
     * Format: SJ-YYYY/KODE_SUP/0001
     */
    public static function generateNomorSuratJalan(?Supplier $supplier = null): string
    {
        $year = now()->format('Y');
        $prefix = 'SUP';

        if ($supplier) {
            $prefix = strtoupper(str_replace([' ', '-', '_'], '', $supplier->kode_supplier ?: 'SUP'));
        }

        $count = self::whereYear('created_at', $year)
            ->when($supplier, fn($q) => $q->where('supplier_id', $supplier->id))
            ->count();

        $seq = $count + 1;
        do {
            $seqStr = str_pad($seq, 4, '0', STR_PAD_LEFT);
            $nomor = "SJ-{$year}/{$prefix}/{$seqStr}";
            $seq++;
        } while (self::where('nomor_surat_jalan', $nomor)->exists());

        return $nomor;
    }
}
