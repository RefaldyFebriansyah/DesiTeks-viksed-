<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'nomor_transaksi',
        'user_id',
        'branch_id',
        'customer_id',
        'total',
        'diskon',
        'pajak',
        'status',
        'catatan',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
