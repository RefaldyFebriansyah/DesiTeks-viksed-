<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingGood extends Model
{
    protected $fillable = [
        'nomor_faktur',
        'supplier_id',
        'user_id',
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
}
