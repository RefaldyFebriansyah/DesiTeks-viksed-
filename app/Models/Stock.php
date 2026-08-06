<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'fabric_id',
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

    public function getStatusAttribute(): string
    {
        if ($this->stok_meter <= 0 && $this->stok_rol <= 0) {
            return 'habis';
        }
        if ($this->stok_meter <= ($this->fabric->stok_minimum ?? 10)) {
            return 'menipis';
        }
        return 'tersedia';
    }
}
