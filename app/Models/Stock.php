<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use BelongsToBranch;

    public $timestamps = false;

    protected $fillable = [
        'fabric_id',
        'branch_id',
        'stok_rol',
        'stok_meter',
        'updated_at',
    ];

    protected $casts = [
        'stok_meter' => 'decimal:2',
        'updated_at' => 'datetime',
    ];

    public function fabric()
    {
        return $this->belongsTo(Fabric::class);
    }

    public function getTotalMeterAttribute(): float
    {
        $meterPerRol = (float) ($this->fabric?->meter_per_rol > 0 ? $this->fabric->meter_per_rol : 50);
        return (float) (($this->stok_meter ?? 0) + (($this->stok_rol ?? 0) * $meterPerRol));
    }

    public function getStatusAttribute(): string
    {
        if ($this->fabric) {
            return $this->fabric->status_stok;
        }

        $rol = (int) ($this->stok_rol ?? 0);
        $meter = (float) ($this->stok_meter ?? 0);

        if ($rol <= 0 && $meter <= 0) {
            return 'habis';
        }
        if ($rol <= 5) {
            return 'menipis';
        }
        return 'aman';
    }
}
