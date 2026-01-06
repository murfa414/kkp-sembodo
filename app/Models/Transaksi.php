<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    //Daftar kolom yang boleh diisi
    protected $fillable = [
        'tanggal_sewa',
        'nama_penyewa',
        'jenis_armada',
        'kategori_armada',
        'layanan',
        'jumlah_sewa',
        'durasi',
        'total_harga'
    ];
}