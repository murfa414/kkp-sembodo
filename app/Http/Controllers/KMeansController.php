<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KMeansController extends Controller
{
    public function index()
    {
        // 1. Cek data transaksi
        if (!Transaksi::exists()) {
            session()->forget('kmeans_result');

            return redirect()
                ->route('upload.index')
                ->with('error', 'Sistem tidak menemukan data yang dapat diproses.');
        }

        // 2. Tampilkan hasil jika sudah ada
        if (session()->has('kmeans_result')) {
            return view('kmeans.result', session('kmeans_result'));
        }

        // 3. Tampilkan halaman konfigurasi (Analisis Manual)
        return view('kmeans.index');
    }

    public function process()
    {
        // =========================
        // KONFIGURASI TETAP
        // =========================
        $k = 3; // FIXED: selalu 3 klaster

        // =========================
        // AMBIL DATA DARI DATABASE
        // =========================
        $dataMentah = Transaksi::select(
            'jenis_armada',
            DB::raw('COUNT(*) as frekuensi'),
            DB::raw('SUM(jumlah_sewa) as total_unit'),
            DB::raw("SUM(CASE WHEN layanan = 'Lepas Kunci' THEN jumlah_sewa ELSE 0 END) as unit_lk"),
            DB::raw("SUM(CASE WHEN layanan = 'Dengan Driver' THEN jumlah_sewa ELSE 0 END) as unit_dd")
        )
            ->groupBy('jenis_armada')
            ->get();

        $dataArmada = [];
        foreach ($dataMentah as $row) {
            $dataArmada[] = [
                'nama' => $row->jenis_armada,
                'c1' => (int) $row->frekuensi,
                'c2' => (int) $row->total_unit,
                'lepas_kunci' => (int) $row->unit_lk,
                'driver' => (int) $row->unit_dd,
            ];
        }

        if (count($dataArmada) < $k) {
            return back()->with('error', 'Data tidak cukup untuk dilakukan klasterisasi.');
        }

        // =========================
        // ALGORITMA K-MEANS
        // =========================
        shuffle($dataArmada);
        $centroids = array_slice($dataArmada, 0, $k);

        $maxIterasi = 100;
        $iterasi = 0;
        $converged = false;
        $clusters = [];

        while (!$converged && $iterasi < $maxIterasi) {
            $iterasi++;
            $clusters = array_fill(0, $k, []);

            // Hitung jarak & tentukan klaster
            foreach ($dataArmada as $i => $data) {
                $minDistance = null;
                $clusterIndex = 0;

                foreach ($centroids as $c => $center) {
                    $distance = sqrt(
                        pow($data['c1'] - $center['c1'], 2) +
                        pow($data['c2'] - $center['c2'], 2)
                    );

                    if ($minDistance === null || $distance < $minDistance) {
                        $minDistance = $distance;
                        $clusterIndex = $c;
                    }
                }

                $dataArmada[$i]['klaster'] = $clusterIndex;
                $clusters[$clusterIndex][] = $dataArmada[$i];
            }

            // Hitung centroid baru
            $newCentroids = [];
            foreach ($clusters as $i => $members) {
                if (count($members)) {
                    $newCentroids[$i] = [
                        'c1' => array_sum(array_column($members, 'c1')) / count($members),
                        'c2' => array_sum(array_column($members, 'c2')) / count($members),
                    ];
                } else {
                    $newCentroids[$i] = $centroids[$i];
                }
            }

            $converged = $this->isCentroidsSame($centroids, $newCentroids);
            $centroids = $newCentroids;
        }

        // =========================
        // RANKING KLASTER
        // =========================
        $scores = [];
        foreach ($centroids as $i => $c) {
            $scores[$i] = $c['c1'] + $c['c2'];
        }

        arsort($scores);

        $mapping = [];
        $rank = 0;
        foreach ($scores as $old => $score) {
            $mapping[$old] = $rank++;
        }

        foreach ($dataArmada as $i => $data) {
            $dataArmada[$i]['klaster'] = $mapping[$data['klaster']];
        }

        $sortedClusters = [];
        foreach ($clusters as $old => $members) {
            $sortedClusters[$mapping[$old]] = $members;
        }

        ksort($sortedClusters);

        foreach ($sortedClusters as $key => $members) {
            usort($sortedClusters[$key], fn($a, $b) => $b['c2'] <=> $a['c2']);
        }

        // =========================
        // SIMPAN KE SESSION
        // =========================
        session([
            'kmeans_result' => compact('dataArmada', 'centroids', 'iterasi', 'sortedClusters')
        ]);

        return redirect()->route('kmeans.index');
    }

    public function reset()
    {
        session()->forget('kmeans_result');
        return redirect()->route('kmeans.index');
    }

    private function isCentroidsSame($old, $new)
    {
        foreach ($old as $i => $c) {
            if ($c['c1'] != $new[$i]['c1'] || $c['c2'] != $new[$i]['c2']) {
                return false;
            }
        }
        return true;
    }
}
