<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'branch_id',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'role' => 'string',
        'status' => 'string',
        'two_factor_enabled' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function incomingGoods()
    {
        return $this->hasMany(IncomingGood::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Helper
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGudang(): bool
    {
        return $this->role === 'gudang';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
