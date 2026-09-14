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
        'supplier_id',
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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
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

    public function isSupplier(): bool
    {
        return $this->role === 'supplier';
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Sensor Email Pengguna untuk Privasi Akses Admin
     */
    public function getMaskedEmailAttribute(): string
    {
        if (!$this->email || !str_contains($this->email, '@')) {
            return '***@***.com';
        }
        [$username, $domain] = explode('@', $this->email, 2);
        $len = strlen($username);
        $maskedUser = ($len <= 2) ? substr($username, 0, 1) . '***' : substr($username, 0, 3) . '***';

        $domainParts = explode('.', $domain, 2);
        $domainName = $domainParts[0];
        $tld = isset($domainParts[1]) ? '.' . $domainParts[1] : '';
        $maskedDomain = (strlen($domainName) <= 2 ? substr($domainName, 0, 1) : substr($domainName, 0, 2)) . '***' . $tld;

        return $maskedUser . '@' . $maskedDomain;
    }

    /**
     * Sensor Username Pengguna untuk Privasi Akses Admin
     */
    public function getMaskedUsernameAttribute(): string
    {
        if (!$this->username) return '***';
        $len = strlen($this->username);
        if ($len <= 3) return substr($this->username, 0, 1) . '***';
        return substr($this->username, 0, 3) . '***';
    }
}
