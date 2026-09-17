<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fabric extends Model
{
    protected $fillable = [
        'kode_kain',
        'nama_kain',
        'category_id',
        'jenis_kain',
        'warna',
        'motif',
        'harga_per_meter',
        'harga_per_rol',
        'meter_per_rol',
        'stok_minimum',
        'stok_maksimum',
        'status',
    ];

    protected $casts = [
        'harga_per_meter' => 'decimal:2',
        'harga_per_rol'   => 'decimal:2',
        'meter_per_rol'   => 'decimal:2',
        'stok_minimum'    => 'integer',
        'stok_maksimum'   => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function fabricRolls()
    {
        return $this->hasMany(FabricRoll::class);
    }

    public function incomingGoodsDetails()
    {
        return $this->hasMany(IncomingGoodsDetail::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // Helper: Total meter gabungan (eceran + rol utuh * meter_per_rol)
    public function getTotalStokMeterAttribute(): float
    {
        $stok = $this->stock;
        if (!$stok) return 0.0;
        $mPerRol = (float) ($this->meter_per_rol > 0 ? $this->meter_per_rol : 50);
        return (float) (($stok->stok_meter ?? 0) + (($stok->stok_rol ?? 0) * $mPerRol));
    }

    // Helper: Get status stok dinamis berdasarkan stok_minimum & stok_maksimum
    public function getStatusStokAttribute(): string
    {
        $stok = $this->stock;
        if (!$stok) {
            return 'habis';
        }
        $rol = (int) ($stok->stok_rol ?? 0);
        $meter = (float) ($stok->stok_meter ?? 0);
        $minRol = 5;
        $maxRol = (int) ($this->stok_maksimum > 0 ? $this->stok_maksimum : 25);

        if ($rol <= 0 && $meter <= 0) {
            return 'habis';
        }
        if ($maxRol > 0 && $rol >= $maxRol) {
            return 'penuh';
        }
        if ($rol <= $minRol) {
            return 'menipis';
        }
        return 'aman';
    }
}
