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

        // --- HELPER: MAP GAMBAR ARMADA ---
        $mapImage = function ($items) {
            return $items->map(function ($item) {
                $namaMobil = strtolower($item->jenis_armada);
                $namaFile = 'default.png';

                // LOGIKA MAPPING GAMBAR
                if (str_contains($namaMobil, 'xpander')) $namaFile = 'mitsubishi_xpander_utimate.png';
                elseif (str_contains($namaMobil, 'innova')) $namaFile = 'toyota_innova_zenix.png';
                elseif (str_contains($namaMobil, 'fortuner')) $namaFile = 'toyota_fortuner_vrz.png';
                elseif (str_contains($namaMobil, 'alphard') || str_contains($namaMobil, 'vellfire')) $namaFile = 'toyota_alphard.png';
                elseif (str_contains($namaMobil, 'avanza') || str_contains($namaMobil, 'veloz')) $namaFile = 'toyota_avanza.png';
                elseif (str_contains($namaMobil, 'hiace')) $namaFile = 'toyota_hiace.png';
                elseif (str_contains($namaMobil, 'pajero')) $namaFile = 'pajero.png';
                elseif (str_contains($namaMobil, 'pariwisata') || str_contains($namaMobil, 'bus')) $namaFile = 'bus_pariwisata.png';
                elseif (str_contains($namaMobil, 'elf')) $namaFile = 'isuzu_elf.jpg';
                elseif (str_contains($namaMobil, 'brio')) $namaFile = 'honda_brio.png';
                elseif (str_contains($namaMobil, 'civic')) $namaFile = 'honda_civic.png';
                elseif (str_contains($namaMobil, 'hrv')) $namaFile = 'honda_hrv.png';
                elseif (str_contains($namaMobil, 'crv')) $namaFile = 'honda_crv.png';
                elseif (str_contains($namaMobil, 'wr-v')) $namaFile = 'honda_wrv.png';
                elseif (str_contains($namaMobil, 'stargazer')) $namaFile = 'hyundai_stargazer.png';
                elseif (str_contains($namaMobil, 'creta')) $namaFile = 'hyundai_creta.png';
                elseif (str_contains($namaMobil, 'ioniq')) $namaFile = 'hyundai_ioniq.png';
                elseif (str_contains($namaMobil, 'palisade')) $namaFile = 'hyundai_palisade.jpg';
                elseif (str_contains($namaMobil, 'kona')) $namaFile = 'hyundai_kona.jpg';
                elseif (str_contains($namaMobil, 'h-1')) $namaFile = 'hyundai_h1.jpg';
                elseif (str_contains($namaMobil, 'ertiga') || str_contains($namaMobil, 'xl7')) $namaFile = 'suzuki_ertiga.png';
                elseif (str_contains($namaMobil, 'xenia')) $namaFile = 'daihatsu_xenia.png';
                elseif (str_contains($namaMobil, 'terios')) $namaFile = 'daihatsu_terios.png';
                elseif (str_contains($namaMobil, 'luxio') || str_contains($namaMobil, 'grandmax')) $namaFile = 'daihatsu_granmax.jpg';
                elseif (str_contains($namaMobil, 'wuling') || str_contains($namaMobil, 'almaz') || str_contains($namaMobil, 'confero')) $namaFile = 'wuling_almaz.png';
                elseif (str_contains($namaMobil, 'camry')) $namaFile = 'toyota_camry.avif';
                elseif (str_contains($namaMobil, 'voxy')) $namaFile = 'toyota_voxy.png';
                elseif (str_contains($namaMobil, 'rush')) $namaFile = 'toyota_rush.png';
                elseif (str_contains($namaMobil, 'mercy') || str_contains($namaMobil, 'mercedes')) $namaFile = 'mercedes_benz_sprinter.jpg';

                // Cek file exist
                if (file_exists(public_path('images/cars/' . $namaFile))) {
                    $item->image = $namaFile;
                } else {
                    $item->image = 'default.png';
                }
                return $item;
            });
        };

        // TERAPKAN MAPPING
        $topArmada = $mapImage($topArmada);
        $topLepasKunci = $mapImage($topLepasKunci);
        $topDriver = $mapImage($topDriver);


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