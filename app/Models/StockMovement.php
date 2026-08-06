<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'fabric_id',
        'user_id',
        'jenis',
        'jumlah_rol',
        'jumlah_meter',
        'keterangan',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'jumlah_meter' => 'decimal:2',
    ];

    public function fabric()
    {
        return $this->belongsTo(Fabric::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
