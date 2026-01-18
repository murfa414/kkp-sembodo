<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Tambah kolom baru
            $table->foreignId('pelanggan_id')->nullable()->after('id')->constrained('pelanggan')->nullOnDelete();
            $table->foreignId('kendaraan_id')->nullable()->after('pelanggan_id')->constrained('kendaraan')->nullOnDelete();
            $table->string('no_invoice', 20)->nullable()->after('kendaraan_id');
            $table->string('durasi_tipe', 50)->nullable()->after('layanan'); // Harian, Bulanan, Fullday, 12 Jam
            $table->string('wilayah', 50)->nullable()->after('durasi_tipe'); // Dalam Kota, Luar Kota, Luar Pulau
            $table->text('keterangan')->nullable()->after('total_harga'); // Deskripsi asli
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
            $table->dropForeign(['kendaraan_id']);
            $table->dropColumn(['pelanggan_id', 'kendaraan_id', 'no_invoice', 'durasi_tipe', 'wilayah', 'keterangan']);
        });
    }
};
