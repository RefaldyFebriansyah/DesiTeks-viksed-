<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'nama_depan_toko'    => 'Kain',
            'nama_belakang_toko' => 'Kita',
            'nama_toko'          => 'KainKita',
            'alamat_toko'        => 'Kosan Dinar, Ciamis, Jawa Barat',
            'telepon_toko'       => '0812-3456-7890',
            'catatan_struk'      => 'Terima kasih atas kunjungan Anda! Kain yang sudah dipotong tidak dapat ditukar/dikembalikan.',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
