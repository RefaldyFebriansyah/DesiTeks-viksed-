<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabric_rolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabric_id')->constrained('fabrics')->onDelete('cascade');
            $table->string('kode_rol')->unique();
            $table->decimal('panjang_meter', 10, 2)->default(0);
            $table->enum('status', ['tersedia', 'terjual'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabric_rolls');
    }
};
