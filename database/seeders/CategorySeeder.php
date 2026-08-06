<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Cotton', 'deskripsi' => 'Kain berbahan kapas alami, nyaman dipakai dan mudah menyerap keringat.'],
            ['nama_kategori' => 'Linen', 'deskripsi' => 'Kain dari serat rami, ringan dan cocok untuk iklim panas.'],
            ['nama_kategori' => 'Rayon', 'deskripsi' => 'Kain semi-sintetis yang lembut dan jatuh dengan baik.'],
            ['nama_kategori' => 'Polyester', 'deskripsi' => 'Kain sintetis yang tahan lama dan tidak mudah kusut.'],
            ['nama_kategori' => 'Batik', 'deskripsi' => 'Kain motif batik khas Indonesia, tersedia dalam berbagai corak.'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
