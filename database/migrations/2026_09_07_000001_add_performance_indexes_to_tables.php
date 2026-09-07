<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fabrics', function (Blueprint $table) {
            $table->index('nama_kain', 'fabrics_nama_kain_index');
            $table->index('status', 'fabrics_status_index');
            $table->index('warna', 'fabrics_warna_index');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->index('nama_supplier', 'suppliers_nama_supplier_index');
        });

        Schema::table('incoming_goods', function (Blueprint $table) {
            $table->index('nomor_faktur', 'incoming_goods_nomor_faktur_index');
            $table->index('tanggal', 'incoming_goods_tanggal_index');
        });
    }

    public function down(): void
    {
        Schema::table('fabrics', function (Blueprint $table) {
            $table->dropIndex('fabrics_nama_kain_index');
            $table->dropIndex('fabrics_status_index');
            $table->dropIndex('fabrics_warna_index');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex('suppliers_nama_supplier_index');
        });

        Schema::table('incoming_goods', function (Blueprint $table) {
            $table->dropIndex('incoming_goods_nomor_faktur_index');
            $table->dropIndex('incoming_goods_tanggal_index');
        });
    }
};
