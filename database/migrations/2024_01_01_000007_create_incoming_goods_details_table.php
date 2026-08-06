<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_goods_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_good_id')->constrained('incoming_goods')->onDelete('cascade');
            $table->foreignId('fabric_id')->constrained('fabrics')->onDelete('restrict');
            $table->integer('jumlah_rol')->default(0);
            $table->decimal('jumlah_meter', 10, 2)->default(0);
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_goods_details');
    }
};
