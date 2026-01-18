<?php

namespace App\Imports;

use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Kendaraan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TransaksiImport implements ToCollection, WithHeadingRow
{
    // --- FUNGSI HEADING ROW ---
    public function headingRow(): int
    {
        return 6; // Header ada di Baris 6
    }
    // ----------------------------

    public function collection(Collection $rows)
    {
        // Validasi Template
        $firstRow = $rows->first();
        if (!isset($firstRow['customer_date'])) {
            throw new \Exception("TEMPLATE_SALAH");
        }

        $currentCustomer = null;
        $currentPelangganId = null;

        foreach ($rows as $row) 
        {
            // 1. Deteksi Baris Header Customer
            if (empty($row['no']) && !empty($row['customer_date'])) {
                $currentCustomer = $row['customer_date'];
                
                // Cari atau buat pelanggan
                $pelanggan = Pelanggan::firstOrCreate(
                    ['nama' => $currentCustomer]
                );
                $currentPelangganId = $pelanggan->id;
            }
            
            // 2. Deteksi Baris Transaksi
            elseif (!empty($row['no'])) {
                
                $rawProduct = strtolower($row['product'] ?? '');
                $rawDesc    = strtolower($row['description'] ?? '');
                $finalArmada = null;

                // --- DAFTAR KATA KUNCI LENGKAP (MERK + MODEL + TIPE) ---
                $merkValid = [
                    // A. MERK MOBIL (Brands)
                    'toyota', 'daihatsu', 'mitsubishi', 'honda', 'suzuki', 
                    'hyundai', 'wuling', 'nissan', 'mercedes', 'bmw', 
                    'isuzu', 'mazda', 'kia', 'lexus', 'volvo', 'mercy',
                    
                    // B. MODEL/TIPE SPESIFIK (Models)
                    'alphard', 'vellfire', 'fortuner', 'innova', 'avanza', 'veloz',
                    'xenia', 'luxio', 'hiace', 'premio', 'elf', 'gran max',
                    'camry', 'civic', 'accord', 'brio', 'jazz', 'mobilio', 'brv', 'hrv', 'crv',
                    'xpander', 'expander', 'pajero', 'triton', 'l300', 
                    'terios', 'rush', 'agya', 'ayla', 'calya', 'sigra',
                    'stargazer', 'creta', 'palisade', 'ioniq', 'kona',
                    'almaz', 'cortez', 'confero', 'air ev', 'binguo',
                    'voxy', 'sprinter',

                    // C. JENIS KENDARAAN (Types)
                    'bus', 'medium bus', 'big bus', 'micro bus', 'coaster'
                ];

                // CEK 1: Apakah kolom Produk berisi Mobil?
                foreach ($merkValid as $keyword) {
                    if (str_contains($rawProduct, $keyword)) {
                        $finalArmada = $row['product']; 
                        break;
                    }
                }

                // CEK 2: Jurus Penyelamat (Cek Deskripsi jika Produk gagal)
                if ($finalArmada === null) {
                    foreach ($merkValid as $keyword) {
                        if (str_contains($rawDesc, $keyword)) {
                            // KETEMU DI DESKRIPSI!
                            if (str_contains($keyword, 'bus')) $finalArmada = 'Bus Pariwisata';
                            elseif ($keyword == 'elf') $finalArmada = 'Isuzu Elf';
                            elseif ($keyword == 'hiace') $finalArmada = 'Toyota Hiace';
                            elseif ($keyword == 'innova') $finalArmada = 'Toyota Innova';
                            elseif ($keyword == 'avanza') $finalArmada = 'Toyota Avanza';
                            elseif ($keyword == 'xpander' || $keyword == 'expander') $finalArmada = 'Mitsubishi Xpander';
                            elseif ($keyword == 'pajero') $finalArmada = 'Mitsubishi Pajero';
                            else $finalArmada = ucwords($keyword); 
                            break;
                        }
                    }
                }

                // EKSEKUSI AKHIR:
                if ($finalArmada === null) {
                    continue; 
                }

                // --- PARSING KENDARAAN (NAMA + PLAT NOMOR) ---
                $kendaraanData = $this->parseKendaraan($finalArmada);
                
                // Cari atau buat kendaraan
                $kendaraan = Kendaraan::firstOrCreate(
                    [
                        'nama' => $kendaraanData['nama'],
                        'plat_nomor' => $kendaraanData['plat_nomor']
                    ]
                );

                $namaPenyewa = $currentCustomer ?? 'Umum';
                $deskripsi = $row['description'] ?? '';
                $layanan = $this->detectLayanan($deskripsi, $finalArmada);
                
                Transaksi::create([
                    // Relasi baru
                    'pelanggan_id'    => $currentPelangganId,
                    'kendaraan_id'    => $kendaraan->id,
                    'no_invoice'      => $row['no'] ?? null,
                    
                    // Data lama (tetap diisi untuk backward compatibility)
                    'tanggal_sewa'    => $this->transformDate($row['customer_date']),
                    'nama_penyewa'    => $namaPenyewa,
                    'jenis_armada'    => $kendaraanData['nama'],  // Nama kendaraan saja (tanpa plat)
                    
                    // Layanan & Durasi
                    'layanan'         => $layanan,
                    'durasi_tipe'     => $this->detectDurasiTipe($deskripsi),
                    'wilayah'         => $this->detectWilayah($deskripsi),
                    
                    // Qty & Keterangan
                    'jumlah_sewa'     => (isset($row['qty']) && is_numeric($row['qty'])) ? (int)$row['qty'] : 1,
                    'keterangan'      => $deskripsi,
                ]);
            }
        }
    }

    /**
     * Parse nama kendaraan dan plat nomor dari string Product
     * Contoh: "Toyota Alphard G - B 1439 DYA" -> ['nama' => 'Toyota Alphard G', 'plat_nomor' => 'B 1439 DYA']
     */
    private function parseKendaraan($product)
    {
        // Cek apakah ada pola plat nomor (B XXXX XXX)
        if (preg_match('/^(.+?)\s*-\s*([A-Z]\s*\d{1,4}\s*[A-Z]{1,3})$/i', trim($product), $matches)) {
            return [
                'nama' => trim($matches[1]),
                'plat_nomor' => strtoupper(trim($matches[2]))
            ];
        }
        
        // Tidak ada plat nomor
        return [
            'nama' => trim($product),
            'plat_nomor' => null
        ];
    }

    /**
     * Deteksi tipe durasi dari deskripsi
     */
    private function detectDurasiTipe($deskripsi)
    {
        $text = strtolower($deskripsi);
        
        if (str_contains($text, 'bulanan') || str_contains($text, 'monthly') || preg_match('/bulan\s+(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)/i', $text)) {
            return 'Bulanan';
        }
        if (str_contains($text, 'fullday') || str_contains($text, 'full day')) {
            return 'Fullday';
        }
        if (str_contains($text, '12 jam') || str_contains($text, '12 hour')) {
            return '12 Jam';
        }
        if (str_contains($text, '18 jam')) {
            return '18 Jam';
        }
        if (str_contains($text, 'harian') || str_contains($text, 'tanggal')) {
            return 'Harian';
        }
        if (str_contains($text, 'drop off') || str_contains($text, 'take and drop')) {
            return 'Drop Off';
        }
        
        return 'Harian'; // Default
    }

    /**
     * Deteksi wilayah dari deskripsi
     */
    private function detectWilayah($deskripsi)
    {
        $text = strtolower($deskripsi);
        
        if (str_contains($text, 'luar pulau')) {
            return 'Luar Pulau';
        }
        if (str_contains($text, 'luar kota') || str_contains($text, 'out of town')) {
            return 'Luar Kota';
        }
        if (str_contains($text, 'dalam kota') || str_contains($text, 'in the city') || str_contains($text, 'in city') || str_contains($text, 'in town')) {
            return 'Dalam Kota';
        }
        
        return 'Dalam Kota'; // Default
    }

    private function transformDate($value)
    {
        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value);
            }
            return Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }

    private function detectLayanan($deskripsi, $jenisArmada)
    {
        $text   = strtolower($deskripsi);
        $armada = strtolower($jenisArmada);

        // 1. CEK DESKRIPSI (Prioritas Tertinggi)
        if (str_contains($text, 'dengan driver') || 
            str_contains($text, 'dengan pengemudi') || 
            str_contains($text, 'dan pengemudi') || 
            str_contains($text, 'with driver')) {
            return 'Dengan Driver';
        }

        if (str_contains($text, 'lepas kunci') ||
            str_contains($text, 'lepaskunci') ||
            str_contains($text, 'tanpa pengemudi')) {
            return 'Lepas Kunci';
        }

        // 2. CEK JENIS KENDARAAN (Fallback Cerdas)
        if (str_contains($armada, 'bus') || 
            str_contains($armada, 'elf') || 
            str_contains($armada, 'hiace') || 
            str_contains($armada, 'coaster') ||
            str_contains($armada, 'premio') ||
            str_contains($armada, 'sprinter')) {
            return 'Dengan Driver';
        }

        // 3. Default terakhir
        return 'Lepas Kunci'; 
    }
}