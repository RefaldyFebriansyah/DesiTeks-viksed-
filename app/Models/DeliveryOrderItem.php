<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrderItem extends Model
{
    protected $fillable = [
        'delivery_order_id',
        'fabric_id',
        'nama_kain',
        'jenis_kain',
        'warna',
        'jumlah_rol',
        'jumlah_meter',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'jumlah_meter' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal'     => 'decimal:2',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function fabric()
    {
        return $this->belongsTo(Fabric::class);
    }
}
