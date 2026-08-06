<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function fabrics()
    {
        return $this->hasMany(Fabric::class);
    }
}
