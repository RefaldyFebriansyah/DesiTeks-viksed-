<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'email',
        'asal_kota',
        'alamat',
        'no_telepon',
    ];

    public function incomingGoods()
    {
        return $this->hasMany(IncomingGood::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    /**
     * Mutator untuk memastikan nomor telepon selalu tersimpan dalam format +62
     */
    public function setNoTeleponAttribute($value): void
    {
        if (!$value) {
            $this->attributes['no_telepon'] = null;
            return;
        }
        $clean = preg_replace('/[^0-9]/', '', $value);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        } elseif (!str_starts_with($clean, '62')) {
            $clean = '62' . $clean;
        }
        $this->attributes['no_telepon'] = '+' . $clean;
    }

    /**
     * Accessor nomor telepon terformat +62
     */
    public function getFormattedPhoneAttribute(): string
    {
        if (!$this->no_telepon) return '-';
        $clean = preg_replace('/[^0-9]/', '', $this->no_telepon);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        } elseif (!str_starts_with($clean, '62')) {
            $clean = '62' . $clean;
        }
        return '+' . $clean;
    }

    /**
     * Sensor nomor telepon untuk akses Admin (contoh: +62 812-****-789)
     */
    public function getMaskedPhoneAttribute(): string
    {
        $phone = $this->formatted_phone;
        if (!$phone || $phone === '-') return '-';

        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) >= 8) {
            $prefix = '+' . substr($digits, 0, 5);
            $suffix = substr($digits, -3);
            return $prefix . '-****-' . $suffix;
        }
        return '+' . substr($digits, 0, 4) . '****';
    }

    /**
     * Sensor email dengan format bintang-bintang untuk privasi Admin.
     * Contoh: info@kainsejahtera.com -> in***@ka***.com
     */
    public function getMaskedEmailAttribute(): string
    {
        if (!$this->email || !str_contains($this->email, '@')) {
            return '***@***.com';
        }

        $parts = explode('@', $this->email);
        if (count($parts) !== 2) {
            return '******';
        }

        $username = $parts[0];
        $domain   = $parts[1];

        $len = strlen($username);
        $maskedUsername = ($len <= 2) ? substr($username, 0, 1) . '***' : substr($username, 0, 3) . '***';

        $dotPos = strrpos($domain, '.');
        if ($dotPos !== false) {
            $domainName = substr($domain, 0, $dotPos);
            $tld        = substr($domain, $dotPos);
            $prefixLen  = min(2, strlen($domainName));
            $maskedDomain = substr($domainName, 0, $prefixLen) . '***' . $tld;
        } else {
            $maskedDomain = '***';
        }

        return $maskedUsername . '@' . $maskedDomain;
    }

    /**
     * Sensor alamat pabrik/gudang untuk privasi Admin
     */
    public function getMaskedAlamatAttribute(): string
    {
        if (!$this->alamat) return '-';
        $len = strlen($this->alamat);
        if ($len <= 8) return 'Sensor Alamat';
        return substr($this->alamat, 0, 6) . '*** (Sensor Alamat)';
    }
}
