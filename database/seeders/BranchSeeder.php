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
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(['id' => $branch['id']], $branch);
        }

        // Hapus cabang sekunder jika ada, reassign data ke cabang 1
        \DB::table('users')->where('branch_id', '>', 1)->update(['branch_id' => 1]);
        \DB::table('delivery_orders')->where('branch_id', '>', 1)->update(['branch_id' => 1]);
        \DB::table('incoming_goods')->where('branch_id', '>', 1)->update(['branch_id' => 1]);
        \DB::table('stocks')->where('branch_id', '>', 1)->update(['branch_id' => 1]);
        \DB::table('branches')->where('id', '>', 1)->delete();
    }
}
