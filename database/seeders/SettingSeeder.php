<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'nama_toko'     => 'DesiTeks',
            'alamat_toko'   => 'Jl. Kebon Jati No. 45, Bandung',
            'telepon_toko'  => '0812-3456-7890',
            'catatan_struk' => 'Terima kasih atas kunjungan Anda! Kain yang sudah dipotong tidak dapat ditukar/dikembalikan.',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
