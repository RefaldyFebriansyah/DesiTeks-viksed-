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

    /**
     * Sensor email dengan format bintang-bintang untuk privasi.
     * Contoh: info@kainsejahtera.com -> in***@ka***.com
     */
    public function getMaskedEmailAttribute(): string
    {
        if (!$this->email) {
            return '-';
        }

        $parts = explode('@', $this->email);
        if (count($parts) !== 2) {
            return '******';
        }

        $username = $parts[0];
        $domain   = $parts[1];

        // Sensor username: ambil 2 huruf awal, sisanya bintang
        $len = strlen($username);
        if ($len <= 2) {
            $maskedUsername = substr($username, 0, 1) . '***';
        } else {
            $maskedUsername = substr($username, 0, 2) . str_repeat('*', min(5, max(3, $len - 2)));
        }

        // Sensor domain name sebelum TLD
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
}
