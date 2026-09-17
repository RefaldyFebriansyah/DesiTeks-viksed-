<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fabrics', function (Blueprint $table) {
            $table->integer('stok_maksimum')->nullable()->default(100)->after('stok_minimum');
        });
    }

    public function down(): void
    {
        Schema::table('fabrics', function (Blueprint $table) {
            $table->dropColumn('stok_maksimum');
        });
    }
};
