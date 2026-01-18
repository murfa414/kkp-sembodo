<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriList = [
            'SUV',
            'MPV Premium',
            'MPV',
            'Compact SUV',
            'Sedan',
            'City Car',
            'Van',
            'Bus',
            'EV (Electric)',
        ];

        foreach ($kategoriList as $nama) {
            Kategori::firstOrCreate(['nama' => $nama]);
        }
    }
}
