<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya buat akun user (admin, gudang, kasir)
        // Data kain, kategori, supplier, stok dimulakan dari nol (0)
        $this->call([
            UserSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
