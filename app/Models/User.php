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
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'role' => 'string',
        'status' => 'string',
    ];

    // Relationships
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
