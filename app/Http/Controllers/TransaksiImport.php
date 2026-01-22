<?php

namespace App\Imports;

use App\Models\Transaksi;
use App\Models\Kategori; // Import Model Kategori
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class TransaksiImport implements ToModel, WithHeadingRow
{
    /**
     * ATURAN BARIS HEADER
     * Wajib ditambahkan karena header Excel kamu ada di baris ke-6
     */
    public function headingRow(): int
    {
        return 6;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        // 1. AMBIL NAMA UNIT DARI EXCEL
        $namaUnit = $row['product'] ?? null; 

        // 2. JALANKAN DETEKSI OTOMATIS
        $kategoriOtomatis = $this->deteksiKategori($namaUnit);

        // --- LOGIKA RELASI KE TABEL KATEGORI ---
        // Cek di tabel 'kategori' berdasarkan kolom 'nama'.
        // Kalau belum ada, dia buat baru. Kalau sudah ada, dia ambil datanya.
        $kategori = Kategori::firstOrCreate([
            'nama' => $kategoriOtomatis
        ]);

        // 3. OLAH TANGGAL
        $tanggal = null;
        if (isset($row['date'])) { 
             try {
                $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']);
             } catch (\Exception $e) {
                $tanggal = Carbon::parse($row['date']);
             }
        }

        // 4. SIMPAN KE DATABASE
        return new Transaksi([
            'tanggal_sewa'    => $tanggal,
            'no_invoice'      => $row['no'] ?? null,
            'nama_penyewa'    => $row['customer_date'] ?? $row['customer'] ?? 'Umum',
            
            // DATA MOBIL
            'jenis_armada'    => $namaUnit,
            
            // --- KUNCI RELASI ---
            // Kita simpan ID-nya (Foreign Key) yang didapat dari tabel Kategori
            'kategori_id'     => $kategori->id, 
            
            'layanan'         => 'Lepas Kunci',
            'jumlah_sewa'     => $row['qty'] ?? 1,
            'total_harga'     => 0,
            'keterangan'      => $row['description'] ?? null,
        ]);
    }

    /**
     * LOGIKA "OTAK" PENDETEKSI JENIS MOBIL
     * (Tetap sama persis)
     */
    private function deteksiKategori($namaUnit)
    {
        if (!$namaUnit) return 'Lainnya';

        $nama = strtolower($namaUnit);

        // 1. KATEGORI LUXURY MPV
        if (str_contains($nama, 'alphard') || 
            str_contains($nama, 'vellfire') || 
            str_contains($nama, 'voxy') || 
            str_contains($nama, 'staria') ||
            str_contains($nama, 'serena')) {
            return 'MPV Premium';
        }

        // 2. KATEGORI SUV
        if (str_contains($nama, 'fortuner') || 
            str_contains($nama, 'pajero') || 
            str_contains($nama, 'palisade') || 
            str_contains($nama, 'hrv') || 
            str_contains($nama, 'hr-v') || 
            str_contains($nama, 'wr-v') || 
            str_contains($nama, 'wrv') || 
            str_contains($nama, 'rush') || 
            str_contains($nama, 'terios') ||
            str_contains($nama, 'creta') ||
            str_contains($nama, 'santa fe')) {
            return 'SUV';
        }

        // 3. KATEGORI MPV
        if (str_contains($nama, 'xpander') || 
            str_contains($nama, 'innova') || 
            str_contains($nama, 'reborn') || 
            str_contains($nama, 'zenix') || 
            str_contains($nama, 'avanza') || 
            str_contains($nama, 'veloz') ||
            str_contains($nama, 'calya') ||
            str_contains($nama, 'sigra') ||
            str_contains($nama, 'ertiga') ||
            str_contains($nama, 'livina')) {
            return 'MPV';
        }

        // 4. KATEGORI SEDAN
        if (str_contains($nama, 'camry') || 
            str_contains($nama, 'mercedes') || 
            str_contains($nama, 'benz') || 
            str_contains($nama, 'bmw') || 
            str_contains($nama, 'civic') || 
            str_contains($nama, 'altis') ||
            str_contains($nama, 'accord')) {
            return 'Sedan';
        }

        // 5. KATEGORI MINIBUS / VAN
        if (str_contains($nama, 'hiace') || 
            str_contains($nama, 'elf') || 
            str_contains($nama, 'travello') || 
            str_contains($nama, 'pregio')) {
            return 'Minibus';
        }

        // 6. KATEGORI BUS
        if (str_contains($nama, 'bus')) { 
            return 'Bus';
        }

        // 7. KATEGORI MOBIL LISTRIK (EV)
        if (str_contains($nama, 'kona') || 
            str_contains($nama, 'ioniq') || 
            str_contains($nama, 'air ev') || 
            str_contains($nama, 'binguo')) {
            return 'EV (Electric)';
        }
        
        // 8. KATEGORI KOMERSIAL
        if (str_contains($nama, 'grandmax') || 
            str_contains($nama, 'luxio') || 
            str_contains($nama, 'blind van') || 
            str_contains($nama, 'pickup')) {
            return 'Komersial';
        }

        return 'Lainnya';
    }
}