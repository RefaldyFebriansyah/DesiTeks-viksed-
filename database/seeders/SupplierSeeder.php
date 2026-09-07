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
                'email'         => 'contact@tekstilnusantara.co.id',
                'asal_kota'     => 'Bandung',
                'alamat'        => 'Jl. Industri Raya No. 12, Bandung, Jawa Barat',
                'no_telepon'    => '+62227654321',
            ],
            [
                'kode_supplier' => 'SUP002',
                'nama_supplier' => 'CV. Kain Sejahtera',
                'email'         => 'info@kainsejahtera.com',
                'asal_kota'     => 'Jakarta',
                'alamat'        => 'Jl. Pasar Baru No. 45, Jakarta Pusat',
                'no_telepon'    => '+62213456789',
            ],
            [
                'kode_supplier' => 'SUP003',
                'nama_supplier' => 'UD. Batik Makmur',
                'email'         => 'batikmakmur@gmail.com',
                'asal_kota'     => 'Surakarta',
                'alamat'        => 'Jl. Batik Laweyan No. 7, Surakarta, Jawa Tengah',
                'no_telepon'    => '+62271712345',
            ],
            [
                'kode_supplier' => 'SUP004',
                'nama_supplier' => 'PT. Sinar Sutra Abadi',
                'email'         => 'sales@sinarsutra.co.id',
                'asal_kota'     => 'Pekalongan',
                'alamat'        => 'Jl. Urip Sumoharjo No. 88, Pekalongan, Jawa Tengah',
                'no_telepon'    => '+62285421888',
            ],
            [
                'kode_supplier' => 'SUP005',
                'nama_supplier' => 'CV. Mitra Denim Prima',
                'email'         => 'order@mitradenim.com',
                'asal_kota'     => 'Surabaya',
                'alamat'        => 'Kawasan Industri SIER Blok B-14, Surabaya, Jawa Timur',
                'no_telepon'    => '+62318432190',
            ],
            [
                'kode_supplier' => 'SUP006',
                'nama_supplier' => 'PT. Gajah Indah Textile',
                'email'         => 'sales@gajahindahtextile.id',
                'asal_kota'     => 'Solo',
                'alamat'        => 'Jl. Slamet Riyadi No. 340, Surakarta, Jawa Tengah',
                'no_telepon'    => '+62271645220',
            ],
            [
                'kode_supplier' => 'SUP007',
                'nama_supplier' => 'CV. Megah Jaya Brokat',
                'email'         => 'cs@megahjayabrokat.com',
                'asal_kota'     => 'Semarang',
                'alamat'        => 'Jl. Pemuda No. 122, Semarang, Jawa Tengah',
                'no_telepon'    => '+62243548901',
            ],
            [
                'kode_supplier' => 'SUP008',
                'nama_supplier' => 'PT. Prima Woolen Indo',
                'email'         => 'info@primawoolen.co.id',
                'asal_kota'     => 'Tangerang',
                'alamat'        => 'Kawasan Industri Manis Jl. Manis Raya No. 45, Tangerang, Banten',
                'no_telepon'    => '+62215918822',
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::updateOrCreate(
                ['kode_supplier' => $sup['kode_supplier']],
                $sup
            );
        }
    }
}
