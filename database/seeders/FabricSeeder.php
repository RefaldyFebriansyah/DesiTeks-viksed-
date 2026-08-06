<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Fabric;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class FabricSeeder extends Seeder
{
    public function run(): void
    {
        $cotton  = Category::where('nama_kategori', 'Cotton')->first();
        $linen   = Category::where('nama_kategori', 'Linen')->first();
        $rayon   = Category::where('nama_kategori', 'Rayon')->first();
        $batik   = Category::where('nama_kategori', 'Batik')->first();
        $polyester = Category::where('nama_kategori', 'Polyester')->first();

        $fabrics = [
            [
                'kode_kain'      => 'CTN-001',
                'nama_kain'      => 'Cotton Premium Polos',
                'category_id'   => $cotton->id,
                'jenis_kain'     => 'Cotton Combed',
                'warna'          => 'Putih',
                'motif'          => 'Polos',
                'harga_per_meter' => 45000,
                'harga_per_rol'  => 2100000,
                'stok_minimum'   => 20,
                'stok_rol'       => 5,
                'stok_meter'     => 250,
            ],
            [
                'kode_kain'      => 'CTN-002',
                'nama_kain'      => 'Cotton Motif Bunga',
                'category_id'   => $cotton->id,
                'jenis_kain'     => 'Cotton Print',
                'warna'          => 'Merah Muda',
                'motif'          => 'Bunga',
                'harga_per_meter' => 38000,
                'harga_per_rol'  => 1750000,
                'stok_minimum'   => 20,
                'stok_rol'       => 3,
                'stok_meter'     => 150,
            ],
            [
                'kode_kain'      => 'LNN-001',
                'nama_kain'      => 'Linen Premium',
                'category_id'   => $linen->id,
                'jenis_kain'     => 'Linen Natural',
                'warna'          => 'Krem',
                'motif'          => 'Polos',
                'harga_per_meter' => 65000,
                'harga_per_rol'  => 3100000,
                'stok_minimum'   => 15,
                'stok_rol'       => 4,
                'stok_meter'     => 200,
            ],
            [
                'kode_kain'      => 'RYN-001',
                'nama_kain'      => 'Rayon Viscose',
                'category_id'   => $rayon->id,
                'jenis_kain'     => 'Rayon',
                'warna'          => 'Biru Navy',
                'motif'          => 'Polos',
                'harga_per_meter' => 32000,
                'harga_per_rol'  => 1500000,
                'stok_minimum'   => 10,
                'stok_rol'       => 8,
                'stok_meter'     => 8,  // stok menipis
            ],
            [
                'kode_kain'      => 'BTK-001',
                'nama_kain'      => 'Batik Tulis Madura',
                'category_id'   => $batik->id,
                'jenis_kain'     => 'Batik Tulis',
                'warna'          => 'Multi',
                'motif'          => 'Parang',
                'harga_per_meter' => 120000,
                'harga_per_rol'  => 5500000,
                'stok_minimum'   => 5,
                'stok_rol'       => 2,
                'stok_meter'     => 0,  // habis
            ],
            [
                'kode_kain'      => 'PLY-001',
                'nama_kain'      => 'Polyester Satin',
                'category_id'   => $polyester->id,
                'jenis_kain'     => 'Polyester',
                'warna'          => 'Hitam',
                'motif'          => 'Polos',
                'harga_per_meter' => 28000,
                'harga_per_rol'  => 1300000,
                'stok_minimum'   => 20,
                'stok_rol'       => 10,
                'stok_meter'     => 500,
            ],
        ];

        foreach ($fabrics as $fabricData) {
            $stokRol   = $fabricData['stok_rol'];
            $stokMeter = $fabricData['stok_meter'];
            unset($fabricData['stok_rol'], $fabricData['stok_meter']);

            $fabric = Fabric::create($fabricData);

            // Create stock record
            Stock::create([
                'fabric_id'  => $fabric->id,
                'stok_rol'   => $stokRol,
                'stok_meter' => $stokMeter,
                'updated_at' => now(),
            ]);
        }
    }
}
