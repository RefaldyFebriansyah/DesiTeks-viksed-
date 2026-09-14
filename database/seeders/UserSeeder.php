<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Refaldi Febriansyah (Admin)',
                'username'  => 'admin',
                'email'     => 'refaldi.febriansyah@gmail.com',
                'password'  => Hash::make('pakade73'),
                'role'      => 'admin',
                'status'    => 'aktif',
                'branch_id' => 1,
            ],
            [
                'name'      => 'Staff Gudang',
                'username'  => 'gudang',
                'email'     => 'refaldi.febriansyahh@gmail.com',
                'password'  => Hash::make('Ipang123'),
                'role'      => 'gudang',
                'status'    => 'aktif',
                'branch_id' => 1,
            ],
            [
                'name'      => 'Staff Kasir',
                'username'  => 'kasir',
                'email'     => 'refaldi.febriansyahh@gmail.com',
                'password'  => Hash::make('Ipang123'),
                'role'      => 'kasir',
                'status'    => 'aktif',
                'branch_id' => 1,
            ],
            [
                'name'        => 'Budi Santoso (PT. Tekstil Nusantara)',
                'username'    => 'supplier',
                'email'       => 'supplier@tekstilnusantara.co.id',
                'password'    => Hash::make('Ipang123'),
                'role'        => 'supplier',
                'status'      => 'aktif',
                'branch_id'   => 1,
                'supplier_id' => 1,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['username' => $user['username']],
                $user
            );
        }
    }
}
