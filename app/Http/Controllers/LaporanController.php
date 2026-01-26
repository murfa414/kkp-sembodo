<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use PDF;

class LaporanController extends Controller
{
    public function index()
    {
        // 1. CEK DATA: Kalau kosong, tendang balik
        if (!Transaksi::exists()) {
            return redirect()->route('upload.index')->with('error', 'Sistem tidak menemukan data yang dapat diproses. Pastikan Anda telah mengunggah data transaksi yang diperlukan.');
        }

        //AMBIL DARI HASIL ANALISIS TERAKHIR (SESSION)
        //Kalau user sudah pernah klik "Proses" di menu Analisis K-Means, kita pakai data itu.
        if (session()->has('kmeans_result')) {
            $hasilAnalisis = session('kmeans_result');
            $clusters = $hasilAnalisis['clusters'];

            // KMeansController sudah mengurutkan: Index 0 = Laris, 1 = Sedang, 2 = Kurang
            // Kita pakai fungsi formatCluster yang sama biar Top 3-nya rapi
            $laris = $this->formatCluster($clusters[0] ?? []);
            $sedang = $this->formatCluster($clusters[1] ?? []);
            $kurangLaris = $this->formatCluster($clusters[2] ?? []);

            return view('laporan.index', compact('laris', 'sedang', 'kurangLaris'));
        }

        //KALAU BELUM ANALISIS, HITUNG OTOMATIS (FALLBACK)

        // 2. AMBIL DATA (Sama persis kayak KMeansController)
        // Kita ambil SEMUA atribut (Frekuensi & Total Unit) biar akurat
        $dataMentah = Transaksi::select(
            'jenis_armada',
            DB::raw('count(*) as frekuensi'),
            DB::raw('sum(jumlah_sewa) as total_unit')
        )->groupBy('jenis_armada')->get();

        $dataArmada = [];
        foreach ($dataMentah as $row) {
            $dataArmada[] = [
                'nama' => $row->jenis_armada,
                'c1' => $row->frekuensi,
                'c2' => $row->total_unit
            ];
        }

        // 3. JALANKAN K-MEANS OTOMATIS (Hardcode K=3)
        $k = 3;

        // Trik: Sort dulu biar centroid awal menyebar bagus (dari yang paling laku ke ga laku)
        // [MODIFIKASI] Sort berdasarkan Skor Gabungan (c1 + c2)
        $dataArmada = collect($dataArmada)->sortByDesc(function ($item) {
            return $item['c1'] + $item['c2'];
        })->values()->all();

        $totalData = count($dataArmada);

        // [PERBAIKAN LOGIKA CENTROID]
        // Ambil 3 data (Atas, Tengah, Bawah) sebagai centroid awal agar sebaran merata
        // Ini mencegah kelompok "Laris" pecah suara
        if ($totalData >= 3) {
            $centroids = [
                $dataArmada[0],                       // Wakil Kelompok Sultan (Laris)
                $dataArmada[intval($totalData / 2)],  // Wakil Kelompok Menengah (Sedang)
                $dataArmada[$totalData - 1]           // Wakil Kelompok Bawah (Kurang)
            ];
        } else {
            // Fallback kalau datanya dikit banget (< 3 mobil)
            $centroids = array_slice($dataArmada, 0, $k);
        }

        $maxIterasi = 100;
        $iterasi = 0;
        $converged = false;
        $clusters = [];

        while (!$converged && $iterasi < $maxIterasi) {
            $iterasi++;
            // Siapkan 3 wadah kosong
            $clusters = array_fill(0, $k, []);

            // Hitung Jarak
            foreach ($dataArmada as $mobil) {
                $jarakTerdekat = null;
                $klasterTerpilih = null;

                foreach ($centroids as $indexC => $center) {
                    $jarak = sqrt(pow($mobil['c1'] - $center['c1'], 2) + pow($mobil['c2'] - $center['c2'], 2));
                    if ($jarakTerdekat === null || $jarak < $jarakTerdekat) {
                        $jarakTerdekat = $jarak;
                        $klasterTerpilih = $indexC;
                    }
                }
                $clusters[$klasterTerpilih][] = $mobil;
            }

            // Hitung Pusat Baru
            $newCentroids = [];
            foreach ($clusters as $index => $anggota) {
                if (count($anggota) > 0) {
                    $newCentroids[$index] = [
                        'c1' => array_sum(array_column($anggota, 'c1')) / count($anggota),
                        'c2' => array_sum(array_column($anggota, 'c2')) / count($anggota)
                    ];
                } else {
                    $newCentroids[$index] = $centroids[$index];
                }
            }

            if ($this->isCentroidsSame($centroids, $newCentroids))
                $converged = true;
            $centroids = $newCentroids;
        }

        // 4. RANKING KLASTER (Penting! Biar labelnya gak ketuker)
        // Kita hitung skor (c1+c2). Skor Tertinggi = Laris.
        $clusterScores = [];
        foreach ($centroids as $index => $center) {
            $clusterScores[$index] = $center['c1'] + $center['c2'];
        }
        arsort($clusterScores); // Urutkan Besar ke Kecil

        // Petakan hasil sorting ke variabel View
        // Index ke-0 hasil sort = Laris
        // Index ke-1 hasil sort = Sedang
        // Index ke-2 hasil sort = Kurang Laris
        $rankedClusters = [];
        foreach ($clusterScores as $oldIndex => $score) {
            $rankedClusters[] = $clusters[$oldIndex];
        }

        // Kirim ke View dengan format rapi
        $laris = $this->formatCluster($rankedClusters[0] ?? []);
        $sedang = $this->formatCluster($rankedClusters[1] ?? []);
        $kurangLaris = $this->formatCluster($rankedClusters[2] ?? []);

        return view('laporan.index', compact('laris', 'sedang', 'kurangLaris'));
    }

    // Fungsi Pembantu: Cek Centroid
    private function isCentroidsSame($old, $new)
    {
        foreach ($old as $i => $v) {
            if ($v['c1'] != $new[$i]['c1'] || $v['c2'] != $new[$i]['c2'])
                return false;
        }
        return true;
    }

    // Fungsi Pembantu: Format Data & Ambil Top 3
    private function formatCluster($clusterData)
    {
        // [MODIFIKASI] Urutkan anggota klaster berdasarkan SKOR GABUNGAN (Frekuensi + Total Unit) terbesar
        usort($clusterData, function ($a, $b) {
            $scoreA = $a['c1'] + $a['c2'];
            $scoreB = $b['c1'] + $b['c2'];
            return $scoreB <=> $scoreA; // Descending (Besar ke Kecil)
        });

        return [
            'count' => count($clusterData), // Jumlah Mobil
            'items' => $clusterData,         // Semua anggota klaster
            'top3' => array_slice($clusterData, 0, 3) // Ambil 3 teratas buat dashboard
        ];
    }

    public function exportPDF(Request $request)
    {
        // Ambil data hasil analisis dari session
        if (!session()->has('kmeans_result')) {
            return redirect()->route('laporan.index')->with('error', 'Data laporan belum tersedia untuk diekspor.');
        }

        $hasilAnalisis = session('kmeans_result');
        $clusters = $hasilAnalisis['clusters'];

        $laris = $this->formatCluster($clusters[0] ?? []);
        $sedang = $this->formatCluster($clusters[1] ?? []);
        $kurangLaris = $this->formatCluster($clusters[2] ?? []);

        // Kirim data ke view khusus PDF
        $pdf = PDF::loadView('laporan.pdf', compact('laris', 'sedang', 'kurangLaris'));

        // Set paper size
        $pdf->setPaper('A4', 'portrait');

        // Cek mode: preview atau download
        $mode = $request->get('mode', 'download');

        // Generate filename dengan timezone Indonesia
        $filename = 'laporan_kmeans_' . now()->setTimezone('Asia/Jakarta')->format('Ymd_His') . '.pdf';

        if ($mode === 'preview') {
            // Stream PDF (tampilkan di browser)
            return $pdf->stream($filename);
        } else {
            // Download PDF
            return $pdf->download($filename);
        }
    }

}