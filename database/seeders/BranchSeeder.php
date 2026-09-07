<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'id'          => 1,
                'nama_cabang' => 'Cabang Utama (Pusat)',
                'kode_cabang' => 'CBG-001',
                'alamat'      => 'Jl. Kebon Jati No. 45, Bandung',
                'telepon'     => '0812-3456-7890',
                'is_main'     => true,
                'status'      => 'aktif',
            ],
            [
                'id'          => 2,
                'nama_cabang' => 'Cabang Gudang & Distribusi',
                'kode_cabang' => 'CBG-002',
                'alamat'      => 'Jl. Soekarno Hatta No. 120, Bandung',
                'telepon'     => '0812-9876-5432',
                'is_main'     => false,
                'status'      => 'aktif',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(['id' => $branch['id']], $branch);
        }
    }
}
