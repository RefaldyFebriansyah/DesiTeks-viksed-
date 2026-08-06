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
                'name'     => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'status'   => 'aktif',
            ],
            [
                'name'     => 'Staff Gudang',
                'username' => 'gudang',
                'password' => Hash::make('gudang123'),
                'role'     => 'gudang',
                'status'   => 'aktif',
            ],
            [
                'name'     => 'Staff Kasir',
                'username' => 'kasir',
                'password' => Hash::make('kasir123'),
                'role'     => 'kasir',
                'status'   => 'aktif',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
