<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_cabang',
        'kode_cabang',
        'alamat',
        'telepon',
        'is_main',
        'status',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $table = $this->hasMany(User::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function incomingGoods(): HasMany
    {
        return $this->hasMany(IncomingGood::class);
    }
}
