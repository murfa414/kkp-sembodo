<?php

namespace App\Imports;

use App\Models\Transaksi;
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

        foreach ($rows as $row) 
        {
            // 1. Deteksi Baris Header Customer
            if (empty($row['no']) && !empty($row['customer_date'])) {
                $currentCustomer = $row['customer_date'];
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
                    'stargazer', 'creta', 'palisade', 'ioniq',
                    'almaz', 'cortez', 'confero', 'air ev', 'binguo',

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
                

                $namaPenyewa = $currentCustomer ?? 'Umum';
                
                Transaksi::create([
                    'tanggal_sewa' => $this->transformDate($row['customer_date']),
                    'nama_penyewa' => $namaPenyewa,
                    'jenis_armada' => $finalArmada, 
                    
                    // [UPDATE DISINI] Kirim nama armada ke fungsi detektif
                    'layanan'      => $this->detectLayanan($row['description'] ?? '', $finalArmada),
                    
                    'jumlah_sewa'  => (isset($row['qty']) && is_numeric($row['qty'])) ? $row['qty'] : 1,
                ]);
            }
        }
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

    // [UPDATE FUNGSI INI] Tambah parameter $jenisArmada
    private function detectLayanan($deskripsi, $jenisArmada)
    {
        $text   = strtolower($deskripsi);
        $armada = strtolower($jenisArmada);

        // 1. CEK DESKRIPSI (Prioritas Tertinggi)
        // Kalau di deskripsi jelas-jelas ditulis, kita ikutin deskripsi
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
        // Kalau deskripsi gak jelas, kita lihat mobilnya.
        // Bus, Elf, Hiace, Coaster -> BIASANYA PASTI PAKAI DRIVER
        if (str_contains($armada, 'bus') || 
            str_contains($armada, 'elf') || 
            str_contains($armada, 'hiace') || 
            str_contains($armada, 'coaster') ||
            str_contains($armada, 'premio')) { // Hiace Premio
            
            return 'Dengan Driver';
        }

        // 3. Default terakhir (Mobil Kecil default-nya Lepas Kunci)
        return 'Lepas Kunci'; 
    }
}