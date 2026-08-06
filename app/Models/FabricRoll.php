<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FabricRoll extends Model
{
    protected $fillable = [
        'fabric_id',
        'kode_rol',
        'panjang_meter',
        'status',
    ];

    protected $casts = [
        'panjang_meter' => 'decimal:2',
    ];

    public function fabric()
    {
        return $this->belongsTo(Fabric::class);
    }
}
