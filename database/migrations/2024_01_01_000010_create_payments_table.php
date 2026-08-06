<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->unique()->constrained('sales')->onDelete('cascade');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->decimal('kembalian', 15, 2)->default(0);
            $table->enum('metode', ['tunai', 'transfer', 'qris'])->default('tunai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
