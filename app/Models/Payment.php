<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'sale_id',
        'jumlah_bayar',
        'kembalian',
        'metode',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'kembalian'    => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
