<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cari main branch ID (default 1)
        $mainBranch = DB::table('branches')->where('is_main', true)->first();
        $mainBranchId = $mainBranch ? $mainBranch->id : (DB::table('branches')->value('id') ?? 1);

        // Update semua stok yang branch_id-nya null agar memiliki branch_id valid
        DB::table('stocks')
            ->whereNull('branch_id')
            ->update(['branch_id' => $mainBranchId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revert needed
    }
};
