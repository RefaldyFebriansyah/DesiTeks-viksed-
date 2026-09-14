<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE delivery_orders MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'menunggu_approval'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE delivery_orders MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'dikirim'");
        }
    }
};
