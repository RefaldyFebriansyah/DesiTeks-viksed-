<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class IncomingGood extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'nomor_faktur',
        'supplier_id',
        'user_id',
        'branch_id',
        'tanggal',
        'catatan',
        'foto_lampiran',
        'total_pembelian',
        'total_rol',
        'total_meter',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'total_pembelian'  => 'decimal:2',
        'total_meter'      => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(IncomingGoodsDetail::class, 'incoming_good_id');
    }

    /**
     * Generate nomor faktur / surat jalan otomatis sesuai inisial perusahaan supplier
     * dan jumlah transaksi masuk dari perusahaan tersebut ke Desiteks.
     */
    public static function generateNomorFaktur(?string $companyName = null): string
    {
        $prefix = 'SUP';
        $count = 0;

        if ($companyName) {
            $trimmed = trim($companyName);
            
            // Cari supplier untuk menghitung riwayat transaksi ke Desiteks
            $supplier = Supplier::where('nama_supplier', $trimmed)
                ->orWhereRaw('LOWER(nama_supplier) = ?', [strtolower($trimmed)])
                ->first();

            if ($supplier) {
                $count = $supplier->incomingGoods()->count();
            }

            // Ekstraksi inisial nama PT / Supplier
            $cleaned = preg_replace('/^(pt\.?|cv\.?|ud\.?|fa\.?|toko|tb\.?)\s+/i', '', $trimmed);
            $words = preg_split('/[\s\-_.]+/', trim($cleaned));
            $letters = '';
            foreach ($words as $w) {
                if (strlen($w) > 0) {
                    $letters .= strtoupper(substr($w, 0, 1));
                }
            }
            if (strlen($letters) >= 2) {
                $prefix = substr($letters, 0, 4);
            } elseif (strlen($cleaned) >= 3) {
                $prefix = strtoupper(substr($cleaned, 0, 3));
            }
        }

        $year = now()->format('Y');
        $seq = $count + 1; // Transaksi ke-(count + 1) dari supplier ini ke Desiteks

        do {
            $numStr = str_pad($seq, 4, '0', STR_PAD_LEFT);
            $gen = "SJ-{$year}/{$prefix}/{$numStr}";
            $seq++;
        } while (self::where('nomor_faktur', $gen)->exists());

        return $gen;
    }
}
