<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingGoodsDetail extends Model
{
    protected $fillable = [
        'incoming_good_id',
        'fabric_id',
        'jumlah_rol',
        'jumlah_meter',
        'harga_beli',
        'subtotal',
    ];

    protected $casts = [
        'jumlah_meter' => 'decimal:2',
        'harga_beli'   => 'decimal:2',
        'subtotal'     => 'decimal:2',
    ];

    public function incomingGood()
    {
        return $this->belongsTo(IncomingGood::class);
    }

    public function fabric()
    {
        return $this->belongsTo(Fabric::class);
    }
}
