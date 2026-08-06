<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabric_id')->constrained('fabrics')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->enum('jenis', ['barang_masuk', 'penjualan', 'penyesuaian']);
            $table->integer('jumlah_rol')->default(0);
            $table->decimal('jumlah_meter', 10, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->nullableMorphs('reference'); // polymorphic: incoming_goods or sales
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
