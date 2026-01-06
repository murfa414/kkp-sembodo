<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BerandaController extends Controller
{
    public function index()
    {
        // 1. Cek Data Kosong
        if (!Transaksi::exists()) {
            return view('beranda.index', [
                'chartTrend' => [], 'chartLayanan' => [], 
                'topArmada' => [], 'maxScore' => 0,
                'topLepasKunci' => [], 'topDriver' => [] // Tambahan agar tidak error di view
            ]); 
        }

        // --- A. DATA PERFORMA ARMADA (TOP 10 BY SKOR GABUNGAN) ---
        // Ambil data mentah dulu (Grouped)
        $rawArmada = Transaksi::select(
                'jenis_armada',
                DB::raw('count(*) as frequency'),          // Frekuensi Sewa
                DB::raw('sum(jumlah_sewa) as total_unit')  // Total Unit
            )
            ->groupBy('jenis_armada')
            ->get();

        // Hitung Skor & Sort Manual di Collection
        // Rumus: Skor = Frekuensi + Total Unit (Sama seperti Laporan)
        $topArmada = $rawArmada->map(function($item) {
            $item->score = $item->frequency + $item->total_unit;
            return $item;
        })->sortByDesc('score')->take(10); // Ambil 10 Terbaik

        // Cari Skor Tertinggi untuk acuan lebar Progress Bar
        $maxScore = $topArmada->max('score') ?? 1;

        
        // 1. Top 5 Lepas Kunci
        $topLepasKunci = Transaksi::where('layanan', 'Lepas Kunci')
            ->select('jenis_armada', DB::raw('count(*) as freq'), DB::raw('sum(jumlah_sewa) as unit'))
            ->groupBy('jenis_armada')
            ->orderByRaw('(count(*) + sum(jumlah_sewa)) DESC') // Sort by Skor Gabungan (Freq + Unit)
            ->take(5)
            ->get();

        // 2. Top 5 Dengan Driver
        $topDriver = Transaksi::where('layanan', 'Dengan Driver')
            ->select('jenis_armada', DB::raw('count(*) as freq'), DB::raw('sum(jumlah_sewa) as unit'))
            ->groupBy('jenis_armada')
            ->orderByRaw('(count(*) + sum(jumlah_sewa)) DESC') // Sort by Skor Gabungan (Freq + Unit)
            ->take(5)
            ->get();


        // --- B. DATA TREN PENYEWAAN BULANAN (LINE CHART) ---
        Carbon::setLocale('id');
        
        $trendData = Transaksi::select(
                DB::raw("DATE_FORMAT(tanggal_sewa, '%Y-%m') as bulan"),
                DB::raw('SUM(jumlah_sewa) as total')
            )
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        $chartTrend = ['categories' => [], 'data' => []];
        foreach ($trendData as $data) {
            $dateObj = Carbon::createFromFormat('Y-m', $data->bulan);
            $chartTrend['categories'][] = $dateObj->translatedFormat('F Y'); 
            $chartTrend['data'][] = (int) $data->total;
        }


        // --- C. DATA PROPORSI LAYANAN (DONUT CHART) ---
        $layananData = Transaksi::select('layanan', DB::raw('count(*) as total'))
            ->groupBy('layanan')
            ->pluck('total', 'layanan'); 

        $chartLayanan = [
            ['name' => 'Lepas Kunci', 'y' => $layananData['Lepas Kunci'] ?? 0],
            ['name' => 'Dengan Driver', 'y' => $layananData['Dengan Driver'] ?? 0],
        ];

        // Kirim $maxScore (bukan $maxTransaksi lagi)
        // [UPDATE] Tambahkan topLepasKunci & topDriver ke compact
        return view('beranda.index', compact(
            'chartTrend', 'chartLayanan', 'topArmada', 'maxScore', 
            'topLepasKunci', 'topDriver'
        ));
    }
}