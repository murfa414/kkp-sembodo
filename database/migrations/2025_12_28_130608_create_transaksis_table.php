<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_sewa')->nullable();
            $table->string('nama_penyewa')->nullable(); // atau Customer
            $table->string('jenis_armada'); // Product (contoh: Toyota Alphard)
            $table->string('kategori_armada')->nullable(); // SUV, MPV, dll (bisa diisi manual/otomatis)
            $table->string('layanan'); // Lepas Kunci / Dengan Supir (dari Description)
            $table->integer('jumlah_sewa')->default(1); // Qty
            $table->integer('durasi')->default(1); // Kalau mau dihitung nanti
            $table->double('total_harga')->nullable(); // Opsional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
