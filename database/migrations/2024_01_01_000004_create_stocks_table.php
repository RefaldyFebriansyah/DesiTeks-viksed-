<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabric_id')->unique()->constrained('fabrics')->onDelete('cascade');
            $table->integer('stok_rol')->default(0);
            $table->decimal('stok_meter', 10, 2)->default(0);
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
