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
    Schema::table('transaksi', function (Blueprint $table) {
        // Kita hubungkan ke tabel 'kategori' (sesuai code migration lu)
        $table->foreignId('kategori_id')
              ->nullable()
              ->after('jenis_armada') // Posisikan setelah kolom jenis_armada
              ->constrained('kategori') // NAMA TABEL TUJUAN: 'kategori'
              ->onDelete('set null');
    });
}

public function down()
{
    Schema::table('transaksi', function (Blueprint $table) {
        $table->dropForeign(['kategori_id']);
        $table->dropColumn('kategori_id');
    });
}
};
