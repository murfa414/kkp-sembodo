<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    //Daftar kolom yang boleh diisi
    protected $fillable = [
        'pelanggan_id',
        'kendaraan_id',
        'no_invoice',
        'tanggal_sewa',
        'nama_penyewa',
        'jenis_armada',
        'kategori_armada',
        'layanan',
        'durasi_tipe',
        'wilayah',
        'jumlah_sewa',
        'durasi',
        'total_harga',
        'keterangan'
    ];

    /**
     * Relasi: Transaksi milik satu Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    /**
     * Relasi: Transaksi milik satu Kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }
}