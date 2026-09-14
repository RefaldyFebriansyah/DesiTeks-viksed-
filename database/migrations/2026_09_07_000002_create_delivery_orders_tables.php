<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat_jalan')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->date('tanggal_kirim');
            $table->string('nama_supir')->nullable();
            $table->string('plat_nomor')->nullable();
            $table->string('ekspedisi')->nullable();
            $table->text('catatan')->nullable();
            $table->string('foto_surat_jalan')->nullable();
            $table->enum('status', ['dikirim', 'diterima', 'ditolak'])->default('dikirim');
            $table->text('catatan_gudang')->nullable();
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->foreignId('incoming_good_id')->nullable()->constrained('incoming_goods')->nullOnDelete();
            $table->integer('total_rol')->default(0);
            $table->decimal('total_meter', 12, 2)->default(0);
            $table->decimal('total_nominal', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('delivery_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained('delivery_orders')->cascadeOnDelete();
            $table->foreignId('fabric_id')->nullable()->constrained('fabrics')->nullOnDelete();
            $table->string('nama_kain');
            $table->string('jenis_kain')->nullable();
            $table->string('warna')->nullable();
            $table->integer('jumlah_rol')->default(0);
            $table->decimal('jumlah_meter', 12, 2)->default(0);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_order_items');
        Schema::dropIfExists('delivery_orders');
    }
};
