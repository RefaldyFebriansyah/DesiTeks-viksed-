<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'kode_pelanggan',
        'nama',
        'telepon',
        'alamat',
        'tipe',
        'diskon_member',
    ];

    protected $casts = [
        'diskon_member' => 'decimal:2',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
