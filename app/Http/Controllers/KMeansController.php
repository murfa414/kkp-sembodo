<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB; // <--- Wajib ada

class KMeansController extends Controller
{
    public function index()
    {
        // 1. [PRIORITAS UTAMA] Cek Data di Database Dulu!
        // Kalau tabel transaksi kosong, kita anggap semua hasil analisis sebelumnya TIDAK VALID.
        if (!Transaksi::exists()) {

            // Hapus session bekas (Hantu) supaya bersih
            session()->forget('kmeans_result');

            // Tendang ke halaman upload
            return redirect()->route('upload.index')->with('error', 'Sistem tidak menemukan data yang dapat diproses. Pastikan Anda telah mengunggah data transaksi yang diperlukan.');
        }

        // 2. Baru Cek Session (Fitur Stickiness)
        // Kalau data database ADA, baru kita cek apakah user habis melakukan analisis?
        if (session()->has('kmeans_result')) {
            return view('kmeans.result', session('kmeans_result'));
        }

        // 3. Kalau Database ADA tapi belum analisis, tampilkan form konfigurasi
        return view('kmeans.index');
    }

    public function process(Request $request)
    {
        // --- TAHAP 1: PERSIAPAN DATA ---

        // FIX: Cek dulu, kalau ada isinya baru diubah jadi integer.
        if ($request->filled('jumlah_klaster')) {
            $request->merge([
                'jumlah_klaster' => (int) $request->jumlah_klaster
            ]);
        }

        // Validasi input
        $request->validate([
            'jumlah_klaster' => 'required|integer|min:2|max:3',
            'atribut'        => 'required|array|min:1'
        ], [
            'jumlah_klaster.required' => 'Harap mengisi jumlah klaster terlebih dahulu.',
            'jumlah_klaster.integer'  => 'Kolom ini hanya dapat diisi dengan angka bulat (misalnya: 3).',
            'jumlah_klaster.min'      => 'Analisis hanya dapat dilakukan apabila terdapat minimal 2 klaster.',
            'jumlah_klaster.max'      => 'Jumlah klaster terlalu banyak. Maksimal hanya diperbolehkan 3 klaster.',
            'atribut.required'        => 'Variabel belum dipilih. Harap mencentang minimal satu variabel (Frekuensi Sewa atau Total Unit Keluar).'
        ]);

        $k = $request->jumlah_klaster;

        // Ambil atribut yang dipilih user (Array dari Checkbox)
        $atributDipilih = $request->atribut;

        // Query Database: Hitung total & pisahkan Lepas Kunci vs Driver
        $dataMentah = Transaksi::select(
                'jenis_armada',
                DB::raw('count(*) as frekuensi'),
                DB::raw('sum(jumlah_sewa) as total_unit'),
                DB::raw("SUM(CASE WHEN layanan = 'Lepas Kunci' THEN jumlah_sewa ELSE 0 END) as unit_lk"),
                DB::raw("SUM(CASE WHEN layanan = 'Dengan Driver' THEN jumlah_sewa ELSE 0 END) as unit_dd")
            )
            ->groupBy('jenis_armada')
            ->get();

        // Format ulang data + FILTER LOGIC (Ini yang baru)
        $dataArmada = [];
        foreach ($dataMentah as $row) {

            // 1. Cek apakah 'frekuensi' dicentang user?
            if (in_array('frekuensi', $atributDipilih)) {
                $nilaiC1 = $row->frekuensi;
            } else {
                $nilaiC1 = 0; // Kalau gak dicentang, nol-kan biar gak dihitung
            }

            // 2. Cek apakah 'total_unit' dicentang user?
            if (in_array('total_unit', $atributDipilih)) {
                $nilaiC2 = $row->total_unit;
            } else {
                $nilaiC2 = 0; // Kalau gak dicentang, nol-kan biar gak dihitung
            }

            $dataArmada[] = [
                'nama' => $row->jenis_armada,
                'c1'   => $nilaiC1,  // Sumbu X (Sesuai Pilihan)
                'c2'   => $nilaiC2,  // Sumbu Y (Sesuai Pilihan)

                // Data pelengkap buat tabel (tetap diambil buat display)
                'lepas_kunci' => $row->unit_lk,
                'driver'      => $row->unit_dd
            ];
        }

        // Cek jumlah data vs klaster
        if (count($dataArmada) < $k) {
            return back()->with('error', 'Jumlah armada (' . count($dataArmada) . ') terlalu sedikit untuk dibagi menjadi ' . $k . ' klaster.');
        }

        // --- TAHAP 2: ALGORITMA K-MEANS ---

        // A. Inisialisasi Centroid Awal (Acak)
        shuffle($dataArmada);
        $centroids = array_slice($dataArmada, 0, $k);

        $maxIterasi = 100;
        $iterasi = 0;
        $converged = false;
        $clusters = [];

        // B. Looping (Iterasi)
        while (!$converged && $iterasi < $maxIterasi) {
            $iterasi++;

            // Reset wadah klaster
            $clusters = [];
            for ($i = 0; $i < $k; $i++) {
                $clusters[$i] = [];
            }

            // 1. Hitung Jarak & Kelompokkan
            foreach ($dataArmada as $key => $mobil) {
                $jarakTerdekat = null;
                $klasterTerpilih = null;

                foreach ($centroids as $indexC => $center) {
                    $jarak = sqrt(
                        pow($mobil['c1'] - $center['c1'], 2) +
                        pow($mobil['c2'] - $center['c2'], 2)
                    );

                    if ($jarakTerdekat === null || $jarak < $jarakTerdekat) {
                        $jarakTerdekat = $jarak;
                        $klasterTerpilih = $indexC;
                    }
                }

                $dataArmada[$key]['klaster'] = $klasterTerpilih;
                $clusters[$klasterTerpilih][] = $dataArmada[$key];
            }

            // 2. Hitung Pusat Baru
            $newCentroids = [];
            foreach ($clusters as $index => $anggota) {
                if (count($anggota) > 0) {
                    $avgC1 = array_sum(array_column($anggota, 'c1')) / count($anggota);
                    $avgC2 = array_sum(array_column($anggota, 'c2')) / count($anggota);

                    $newCentroids[$index] = [
                        'c1' => $avgC1,
                        'c2' => $avgC2
                    ];
                } else {
                    $newCentroids[$index] = $centroids[$index];
                }
            }

            // 3. Cek Konvergensi
            if ($this->isCentroidsSame($centroids, $newCentroids)) {
                $converged = true;
            }

            $centroids = $newCentroids;
        }

        // --- TAHAP TAMBAHAN: SORTING & RANKING (Laris = 0) ---

        // 1. Hitung total skor tiap centroid
        $clusterScores = [];
        foreach ($centroids as $index => $center) {
            $clusterScores[$index] = $center['c1'] + $center['c2'];
        }

        // 2. Urutkan skor dari TINGGI ke RENDAH
        arsort($clusterScores);

        // 3. Buat Mapping Index
        $mapping = [];
        $rank = 0;
        foreach ($clusterScores as $oldIndex => $score) {
            $mapping[$oldIndex] = $rank;
            $rank++;
        }

        // 4. Terapkan ke Data Armada
        foreach ($dataArmada as $key => $mobil) {
            $oldKlaster = $mobil['klaster'];
            $dataArmada[$key]['klaster'] = $mapping[$oldKlaster];
        }

        // 5. Terapkan ke Clusters & Centroids
        $sortedClusters = [];
        $sortedCentroids = [];
        foreach ($mapping as $oldIndex => $newRank) {
            $sortedClusters[$newRank] = $clusters[$oldIndex];
            $sortedCentroids[$newRank] = $centroids[$oldIndex];
        }

        $clusters = $sortedClusters;
        $centroids = $sortedCentroids;

        ksort($clusters);
        ksort($centroids);

        //SORTING ANGGOTA DALAM KLASTER
        // Biar di Tabel K-Means urutannya Rapi (Paling Laris di Atas)
        foreach ($clusters as $key => $members) {
            usort($clusters[$key], function ($a, $b) {
                // Bandingkan Total Unit Keluar (c2), dari Besar ke Kecil
                return $b['c2'] <=> $a['c2'];
            });
        }

        // 1. Bungkus semua data hasil ke dalam array
        $hasilAnalisis = compact('dataArmada', 'centroids', 'iterasi', 'clusters');

        // 2. Simpan ke Session (Supaya permanen walau pindah halaman)
        session(['kmeans_result' => $hasilAnalisis]);

        // 3. Redirect ke halaman index (Nanti index bakal otomatis nampilin result)
        return redirect()->route('kmeans.index');
    }

    //Untuk tombol "Analisis Ulang"
    public function reset()
    {
        // Hapus data analisis dari session
        session()->forget('kmeans_result');

        // Balik ke form awal
        return redirect()->route('kmeans.index');
    }

    private function isCentroidsSame($old, $new)
    {
        foreach ($old as $index => $val) {
            if ($val['c1'] != $new[$index]['c1'] || $val['c2'] != $new[$index]['c2']) {
                return false;
            }
        }
        return true;
    }
}
