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
     * Mutator untuk memastikan nomor telepon selalu tersimpan dalam format +628...
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

        // Normalkan digit setelah 62 agar selalu diawali 8 (nomor HP Indonesia)
        $after62 = substr($clean, 2);
        if (strlen($after62) > 0 && !str_starts_with($after62, '8')) {
            $clean = '628' . $after62;
        }

        $this->attributes['no_telepon'] = '+' . $clean;
    }

    /**
     * Accessor nomor telepon terformat rapi +62 8xx-xxxx-xxxx
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

        $after62 = substr($clean, 2);
        if (strlen($after62) > 0 && !str_starts_with($after62, '8')) {
            $clean = '628' . $after62;
        }

        $prefix = '+' . substr($clean, 0, 2);
        $body   = substr($clean, 2);

        if (strlen($body) >= 9) {
            return $prefix . ' ' . substr($body, 0, 3) . '-' . substr($body, 3, 4) . '-' . substr($body, 7);
        }
        return $prefix . ' ' . $body;
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
