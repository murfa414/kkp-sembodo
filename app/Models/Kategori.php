<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'nama'
    ];

    /**
     * Relasi: Kategori memiliki banyak kendaraan
     */
    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class, 'kategori_id');
    }
}
