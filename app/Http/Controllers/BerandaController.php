<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cek Data Kosong
        if (!Transaksi::exists()) {
            return view('beranda.index', [
                'chartTrend' => [],
                'chartLayanan' => [],
                'topArmada' => [],
                'maxScore' => 0,
                'topLepasKunci' => [],
                'topDriver' => [],
                'availableMonths' => [],
                'availableBrands' => [],
                'selectedMonth' => null,
                'selectedBrand' => null
            ]);
        }

        // --- AMBIL FILTER DARI REQUEST ---
        $selectedMonth = $request->get('bulan'); // Format: 2025-07
        $selectedBrand = $request->get('brand'); // Format: Toyota, Mitsubishi, dll

        // --- AMBIL DATA UNTUK DROPDOWN FILTER ---
        // A. Daftar Bulan yang Tersedia
        $availableMonths = Transaksi::select(DB::raw("DATE_FORMAT(tanggal_sewa, '%Y-%m') as bulan"))
            ->distinct()
            ->orderBy('bulan', 'desc')
            ->pluck('bulan')
            ->map(function ($bulan) {
                Carbon::setLocale('id');
                $dateObj = Carbon::createFromFormat('Y-m', $bulan);
                return [
                    'value' => $bulan,
                    'label' => $dateObj->translatedFormat('F Y')
                ];
            });

        // B. Daftar Brand yang Tersedia (Ambil dari nama depan jenis_armada)
        $availableBrands = Transaksi::select('jenis_armada')
            ->distinct()
            ->get()
            ->map(function ($item) {
                // Ambil kata pertama sebagai brand
                $parts = explode(' ', $item->jenis_armada);
                return $parts[0];
            })
            ->unique()
            ->values()
            ->sort();

        // --- BUILD BASE QUERY DENGAN FILTER ---
        $baseQuery = Transaksi::query();

        if ($selectedMonth) {
            $baseQuery->whereRaw("DATE_FORMAT(tanggal_sewa, '%Y-%m') = ?", [$selectedMonth]);
        }

        if ($selectedBrand) {
            $baseQuery->where('jenis_armada', 'like', $selectedBrand . '%');
        }

        // --- A. DATA PERFORMA ARMADA (ALL BY SKOR GABUNGAN) ---
        $rawArmada = (clone $baseQuery)
            ->select(
                'jenis_armada',
                DB::raw('count(*) as frequency'),
                DB::raw('sum(jumlah_sewa) as total_unit')
            )
            ->groupBy('jenis_armada')
            ->get();

        $topArmada = $rawArmada->map(function ($item) {
            $item->score = $item->frequency + $item->total_unit;
            return $item;
        })->sortByDesc('score');

        $maxScore = $topArmada->max('score') ?? 1;

        // --- B. TOP 5 LEPAS KUNCI ---
        $topLepasKunci = (clone $baseQuery)
            ->where('layanan', 'Lepas Kunci')
            ->select('jenis_armada', DB::raw('count(*) as freq'), DB::raw('sum(jumlah_sewa) as unit'))
            ->groupBy('jenis_armada')
            ->orderByRaw('(count(*) + sum(jumlah_sewa)) DESC')
            ->take(5)
            ->get();

        // --- C. TOP 5 DENGAN DRIVER ---
        $topDriver = (clone $baseQuery)
            ->where('layanan', 'Dengan Driver')
            ->select('jenis_armada', DB::raw('count(*) as freq'), DB::raw('sum(jumlah_sewa) as unit'))
            ->groupBy('jenis_armada')
            ->orderByRaw('(count(*) + sum(jumlah_sewa)) DESC')
            ->take(5)
            ->get();

        // --- D. DATA TREN PENYEWAAN BULANAN (LINE CHART) ---
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

        // --- E. DATA PROPORSI LAYANAN (DONUT CHART) ---
        $layananData = (clone $baseQuery)
            ->select('layanan', DB::raw('count(*) as total'))
            ->groupBy('layanan')
            ->pluck('total', 'layanan');

        $chartLayanan = [
            ['name' => 'Lepas Kunci', 'y' => $layananData['Lepas Kunci'] ?? 0],
            ['name' => 'Dengan Driver', 'y' => $layananData['Dengan Driver'] ?? 0],
        ];

        // --- F. STATISTIK RINGKASAN ---
        $totalTransaksi = (clone $baseQuery)->count();
        $totalUnit = (clone $baseQuery)->sum('jumlah_sewa');

        return view('beranda.index', compact(
            'chartTrend',
            'chartLayanan',
            'topArmada',
            'maxScore',
            'topLepasKunci',
            'topDriver',
            'availableMonths',
            'availableBrands',
            'selectedMonth',
            'selectedBrand',
            'totalTransaksi',
            'totalUnit'
        ));
    }
}