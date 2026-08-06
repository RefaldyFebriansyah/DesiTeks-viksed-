<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'alamat',
        'no_telepon',
    ];

    public function incomingGoods()
    {
        return $this->hasMany(IncomingGood::class);
    }
}
