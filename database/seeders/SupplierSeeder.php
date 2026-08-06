<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'kode_supplier' => 'SUP001',
                'nama_supplier' => 'PT. Tekstil Nusantara',
                'alamat'        => 'Jl. Industri Raya No. 12, Bandung, Jawa Barat',
                'no_telepon'    => '022-7654321',
            ],
            [
                'kode_supplier' => 'SUP002',
                'nama_supplier' => 'CV. Kain Sejahtera',
                'alamat'        => 'Jl. Pasar Baru No. 45, Jakarta Pusat',
                'no_telepon'    => '021-3456789',
            ],
            [
                'kode_supplier' => 'SUP003',
                'nama_supplier' => 'UD. Batik Makmur',
                'alamat'        => 'Jl. Batik Laweyan No. 7, Surakarta, Jawa Tengah',
                'no_telepon'    => '0271-712345',
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::create($sup);
        }
    }
}
