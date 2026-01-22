<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori'; // Nama tabel (singular)
    protected $fillable = ['nama']; // Kolomnya 'nama', bukan 'nama_kategori'
}