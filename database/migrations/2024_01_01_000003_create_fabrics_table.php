<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabrics', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kain')->unique();
            $table->string('nama_kain');
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('jenis_kain')->nullable();
            $table->string('warna')->nullable();
            $table->string('motif')->nullable();
            $table->decimal('harga_per_meter', 15, 2)->default(0);
            $table->decimal('harga_per_rol', 15, 2)->default(0);
            $table->integer('stok_minimum')->default(10); // minimum stok meter
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabrics');
    }
};
