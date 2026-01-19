@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Sembodo Rent A Car - Analisis Performa')

@section('content')

    {{-- Kondisi 1: Database Kosong --}}
    @if(count($topArmada) == 0)
        <div class="min-h-screen flex items-center justify-center">
            <div class="w-full max-w-2xl">
                <!-- Hero Card -->
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl overflow-hidden shadow-2xl">
                    <div class="px-8 py-12 md:px-12 md:py-16">
                        <div class="text-center md:text-left md:flex md:items-center md:justify-between">
                            <div class="mb-6 md:mb-0 md:flex-1">
                                <h2 class="text-3xl md:text-4xl font-bold text-white mb-3">
                                    Mulai Analisis Sekarang
                                </h2>
                                <p class="text-blue-100 text-lg mb-6">
                                    Impor data transaksi rental Anda untuk melihat insights mendalam tentang performa armada
                                    menggunakan analisis K-Means clustering.
                                </p>
                                <a href="{{ route('upload.index') }}"
                                    class="inline-flex items-center gap-2 bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Impor Data
                                </a>
                            </div>
                            <div class="text-blue-200 text-6xl md:text-8xl opacity-20 md:flex-1 text-center">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="inline-block">
                                    <path
                                        d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kondisi 2: Data ada tapi belum dianalisis --}}
    @elseif(!session()->has('kmeans_result'))
        <div class="min-h-screen flex items-center justify-center">
            <div class="w-full max-w-2xl">
                <div class="bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl overflow-hidden shadow-2xl">
                    <div class="px-8 py-12 md:px-12 md:py-16">
                        <div class="text-center md:text-left md:flex md:items-center md:justify-between">
                            <div class="mb-6 md:mb-0 md:flex-1">
                                <h2 class="text-3xl md:text-4xl font-bold text-white mb-3">
                                    Data Siap Dianalisis
                                </h2>
                                <p class="text-purple-100 text-lg mb-2">
                                    Data transaksi telah berhasil diunggah. Jalankan analisis K-Means untuk mengidentifikasi 3
                                    segmen performa armada Anda.
                                </p>
                                <p class="text-purple-100 text-sm mb-6 opacity-90">
                                    ✓ Laris (High Demand) • ✓ Sedang (Medium) • ✓ Kurang Laris (Low Demand)
                                </p>
                                <a href="{{ route('kmeans.index') }}"
                                    class="inline-flex items-center gap-2 bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Jalankan Analisis
                                </a>
                            </div>
                            <div class="text-purple-200 text-6xl md:text-8xl opacity-20 md:flex-1 text-center">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="inline-block">
                                    <path
                                        d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kondisi 3: Dashboard Lengkap --}}
    @else

        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Dashboard Performa</h1>
            <p class="text-gray-600 dark:text-gray-400">Analisis K-Means Clustering Armada Anda</p>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <!-- Left Column: Fleet Performance -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <h3 class="text-white font-bold text-lg">Performa Armada</h3>
                        <p class="text-blue-100 text-sm">Top 10 Kendaraan</p>
                    </div>

                    <!-- Fleet List -->
                    <div class="p-6 max-h-96 overflow-y-auto">
                        <div class="space-y-4">
                            @foreach($topArmada as $index => $mobil)
                                @php
                                    $rank = $loop->index;
                                    if ($rank < 3) {
                                        $status = 'Laris';
                                        $statusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                        $barColor = 'bg-blue-500';
                                    } elseif ($rank < 7) {
                                        $status = 'Sedang';
                                        $statusColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                                        $barColor = 'bg-yellow-500';
                                    } else {
                                        $status = 'Kurang Laris';
                                        $statusColor = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                        $barColor = 'bg-red-500';
                                    }
                                    $persen = ($mobil->score / $maxScore) * 100;

                                    // Logic Gambar Mobil
                                    $namaMobil = strtolower($mobil->jenis_armada);
                                    $namaFile = 'default.png';

                                    if (str_contains($namaMobil, 'xpander')) {
                                        $namaFile = 'mitsubishi_xpander_utimate.png';
                                    } elseif (str_contains($namaMobil, 'innova')) {
                                        $namaFile = 'toyota_innova_zenix.png';
                                    } elseif (str_contains($namaMobil, 'avanza') || str_contains($namaMobil, 'veloz')) {
                                        $namaFile = 'toyota_avanza.png';
                                    } elseif (str_contains($namaMobil, 'fortuner')) {
                                        $namaFile = 'toyota_fortuner_vrz.png';
                                    } elseif (str_contains($namaMobil, 'hiace')) {
                                        $namaFile = 'toyota_hiace.png';
                                    } elseif (str_contains($namaMobil, 'alphard') || str_contains($namaMobil, 'vellfire')) {
                                        $namaFile = 'toyota_alphard.png';
                                    } elseif (str_contains($namaMobil, 'pajero')) {
                                        $namaFile = 'toyota_pajero.png';
                                    } elseif (str_contains($namaMobil, 'bus')) {
                                        if (str_contains($namaMobil, 'big')) {
                                            $namaFile = 'big_bus.png';
                                        } else {
                                            $namaFile = 'bus_pariwisata.png';
                                        }
                                    }

                                    $imagePath = file_exists(public_path('images/cars/' . $namaFile)) ? asset('images/cars/' . $namaFile) : asset('images/cars/default.png');
                                @endphp

                                <div
                                    class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0 last:pb-0 hover:bg-gray-50 dark:hover:bg-gray-700/30 p-2 rounded-lg transition-colors">
                                    <div class="flex items-center gap-4 mb-2">
                                        <!-- Car Image Thumbnail -->
                                        <div
                                            class="flex-shrink-0 w-16 h-12 bg-gray-100 dark:bg-gray-700 rounded-md overflow-hidden flex items-center justify-center border border-gray-200 dark:border-gray-600">
                                            <img src="{{ $imagePath }}" alt="{{ $mobil->jenis_armada }}"
                                                class="w-full h-full object-contain p-1">
                                        </div>

                                        <div class="flex-grow min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <p class="font-bold text-gray-900 dark:text-white truncate pr-2"
                                                        title="{{ $mobil->jenis_armada }}">
                                                        {{ $loop->iteration }}. {{ $mobil->jenis_armada }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                        {{ $mobil->frequency }} transaksi • {{ $mobil->total_unit }} unit
                                                    </p>
                                                </div>
                                                <span
                                                    class="flex-shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $statusColor }}">
                                                    {{ $status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-1">
                                        <div class="{{ $barColor }} h-1.5 rounded-full transition-all duration-1000 ease-out"
                                            style="width: {{ $persen }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Charts -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Trend Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                        <h3 class="text-white font-bold text-lg">Tren Penyewaan Bulanan</h3>
                        <p class="text-purple-100 text-sm">Total transaksi per bulan</p>
                    </div>
                    <div class="p-6">
                        <div id="trendChart" class="h-64"></div>
                    </div>
                </div>

                <!-- Service Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 px-6 py-4">
                        <h3 class="text-white font-bold text-lg">Proporsi Layanan</h3>
                        <p class="text-cyan-100 text-sm">Distribusi Lepas Kunci vs Dengan Driver</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div id="layananChart" class="h-56"></div>
                            </div>
                            <div class="flex flex-col justify-center space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-4 h-4 bg-blue-500 rounded"></div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Lepas Kunci</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-4 h-4 bg-cyan-500 rounded"></div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Dengan Driver</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Service Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Lepas Kunci Top 5 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"></path>
                    </svg>
                    <div>
                        <h3 class="text-white font-bold">5 Teratas - Lepas Kunci</h3>
                        <p class="text-blue-100 text-xs">Armada paling banyak disewa tanpa driver</p>
                    </div>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        @forelse($topLepasKunci as $item)
                            <li class="flex items-center justify-between p-3 bg-blue-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $loop->iteration }}. {{ $item->jenis_armada }}
                                </span>
                                <span class="px-3 py-1 bg-blue-600 text-white text-xs rounded-full font-semibold">
                                    {{ $item->freq + $item->unit }} Poin
                                </span>
                            </li>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm italic">Data tidak tersedia</p>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Dengan Driver Top 5 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 px-6 py-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                    </svg>
                    <div>
                        <h3 class="text-white font-bold">5 Teratas - Dengan Driver</h3>
                        <p class="text-cyan-100 text-xs">Armada paling banyak disewa dengan driver</p>
                    </div>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        @forelse($topDriver as $item)
                            <li class="flex items-center justify-between p-3 bg-cyan-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $loop->iteration }}. {{ $item->jenis_armada }}
                                </span>
                                <span class="px-3 py-1 bg-cyan-600 text-white text-xs rounded-full font-semibold">
                                    {{ $item->freq + $item->unit }} Poin
                                </span>
                            </li>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm italic">Data tidak tersedia</p>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>



        @push('scripts')
            <script>
                // Trend Chart Configuration
                var trendOptions = {
                    chart: {
                        type: 'area',
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: true,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: true,
                                reset: true
                            }
                        },
                        sparkline: { enabled: false },
                    },
                    colors: ['#8b5cf6'],
                    series: [{
                        name: 'Total Sewa',
                        data: @json($chartTrend['data'] ?? [])
                    }],
                    xaxis: {
                        categories: @json($chartTrend['categories'] ?? []),
                        labels: {
                            style: {
                                fontSize: '12px',
                                colors: '#6b7280'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '12px',
                                colors: '#6b7280'
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [20, 100, 100, 100]
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    tooltip: {
                        theme: 'light',
                        style: {
                            fontSize: '12px'
                        }
                    },
                    dataLabels: { enabled: false }
                };

                var trendChart = new ApexCharts(document.querySelector("#trendChart"), trendOptions);
                trendChart.render();

                // Donut Chart Configuration
                var donutOptions = {
                    chart: {
                        type: 'donut',
                        sparkline: { enabled: false }
                    },
                    colors: ['#3b82f6', '#06b6d4'],
                    series: @json(array_map(function ($item) {
                        return $item['y'] ?? 0;
                    }, $chartLayanan ?? [])),
                    labels: ['Lepas Kunci', 'Dengan Driver'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        fontSize: '14px',
                                        fontWeight: 500
                                    },
                                    value: {
                                        fontSize: '16px',
                                        fontWeight: 700
                                    }
                                }
                            }
                        }
                    },
                    stroke: { width: 0 },
                    dataLabels: {
                        enabled: false
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function (val) {
                                return val.toLocaleString();
                            }
                        }
                    }
                };

                var donutChart = new ApexCharts(document.querySelector("#layananChart"), donutOptions);
                donutChart.render();
            </script>
        @endpush

    @endif

@endsection