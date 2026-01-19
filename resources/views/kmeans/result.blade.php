@extends('layouts.admin')

@section('title', 'Hasil Analisis K-Means')
@section('page-title', 'Hasil Klasterisasi Data')

@section('content')

    <div class="space-y-8">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Hasil Analisis</h1>
                <p class="text-gray-600 dark:text-gray-400 flex items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Konvergensi pada iterasi {{ $iterasi }}
                    </span>
                </p>
            </div>
            <a href="{{ route('laporan.pdf') }}" target="_blank"
                class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                    </path>
                </svg>
                Unduh PDF
            </a>
        </div>

        <!-- Scatter Plot -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                <h2 class="text-white font-bold text-xl">Visualisasi Cluster - Scatter Plot</h2>
                <p class="text-indigo-100 text-sm mt-1">Persebaran data berdasarkan Frekuensi Sewa vs Total Unit</p>
            </div>
            <div class="p-6">
                <div id="kmeansChart" class="h-96"></div>
            </div>
        </div>

        <!-- Cluster Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Cluster 1: Laris (Blue) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border-t-4 border-blue-500">
                <div class="px-6 py-4 bg-blue-50 dark:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Laris</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">High Demand</p>
                        </div>
                        <div class="text-4xl font-bold text-blue-600">🔥</div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="mb-4">
                        <p class="text-3xl font-bold text-blue-600">
                            {{ count(array_filter($sortedClusters[0] ?? [], function ($item) {
        return true; })) }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Jenis Kendaraan</p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase mb-3">Top 3</p>
                        <ul class="space-y-2">
                            @forelse(array_slice($sortedClusters[0] ?? [], 0, 3) as $item)
                                @php
                                    $namaMobil = strtolower($item['nama']);
                                    $namaFile = 'default.png';
                                    if (str_contains($namaMobil, 'xpander')) { $namaFile = 'mitsubishi_xpander_utimate.png'; }
                                    elseif (str_contains($namaMobil, 'innova')) { $namaFile = 'toyota_innova_zenix.png'; }
                                    elseif (str_contains($namaMobil, 'avanza') || str_contains($namaMobil, 'veloz')) { $namaFile = 'toyota_avanza.png'; }
                                    elseif (str_contains($namaMobil, 'fortuner')) { $namaFile = 'toyota_fortuner_vrz.png'; }
                                    elseif (str_contains($namaMobil, 'hiace')) { $namaFile = 'toyota_hiace.png'; }
                                    elseif (str_contains($namaMobil, 'alphard') || str_contains($namaMobil, 'vellfire')) { $namaFile = 'toyota_alphard.png'; }
                                    elseif (str_contains($namaMobil, 'pajero')) { $namaFile = 'pajero.png'; }
                                    elseif (str_contains($namaMobil, 'bus')) {
                                        if (str_contains($namaMobil, 'big')) { $namaFile = 'big_bus.png'; } else { $namaFile = 'bus_pariwisata.png'; }
                                    }
                                    $imagePath = file_exists(public_path('images/cars/' . $namaFile)) ? asset('images/cars/' . $namaFile) : asset('images/cars/default.png');
                                @endphp
                                <li class="flex items-center justify-between text-sm py-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200">
                                            <img src="{{ $imagePath }}" alt="" class="w-full h-full object-contain">
                                        </div>
                                        <div>
                                            <div class="text-gray-900 dark:text-white font-medium text-xs leading-tight">
                                                {{ $loop->iteration }}. {{ $item['nama'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-1.5 py-0.5 rounded font-bold">Top</span>
                                </li>
                            @empty
                                <p class="text-xs text-gray-500 italic">Tidak ada data</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Cluster 2: Sedang (Yellow) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border-t-4 border-yellow-500">
                <div class="px-6 py-4 bg-yellow-50 dark:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sedang</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Medium Demand</p>
                        </div>
                        <div class="text-4xl font-bold text-yellow-600">⚡</div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="mb-4">
                        <p class="text-3xl font-bold text-yellow-600">
                            {{ count(array_filter($sortedClusters[1] ?? [], function ($item) {
        return true; })) }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Jenis Kendaraan</p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase mb-3">Top 3</p>
                        <ul class="space-y-2">
                            @forelse(array_slice($sortedClusters[1] ?? [], 0, 3) as $item)
                                @php
                                    $namaMobil = strtolower($item['nama']);
                                    $namaFile = 'default.png';
                                    if (str_contains($namaMobil, 'xpander')) { $namaFile = 'mitsubishi_xpander_utimate.png'; }
                                    elseif (str_contains($namaMobil, 'innova')) { $namaFile = 'toyota_innova_zenix.png'; }
                                    elseif (str_contains($namaMobil, 'avanza') || str_contains($namaMobil, 'veloz')) { $namaFile = 'toyota_avanza.png'; }
                                    elseif (str_contains($namaMobil, 'fortuner')) { $namaFile = 'toyota_fortuner_vrz.png'; }
                                    elseif (str_contains($namaMobil, 'hiace')) { $namaFile = 'toyota_hiace.png'; }
                                    elseif (str_contains($namaMobil, 'alphard') || str_contains($namaMobil, 'vellfire')) { $namaFile = 'toyota_alphard.png'; }
                                    elseif (str_contains($namaMobil, 'pajero')) { $namaFile = 'pajero.png'; }
                                    elseif (str_contains($namaMobil, 'bus')) {
                                        if (str_contains($namaMobil, 'big')) { $namaFile = 'big_bus.png'; } else { $namaFile = 'bus_pariwisata.png'; }
                                    }
                                    $imagePath = file_exists(public_path('images/cars/' . $namaFile)) ? asset('images/cars/' . $namaFile) : asset('images/cars/default.png');
                                @endphp
                                <li class="flex items-center justify-between text-sm py-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200">
                                            <img src="{{ $imagePath }}" alt="" class="w-full h-full object-contain">
                                        </div>
                                        <div>
                                            <div class="text-gray-900 dark:text-white font-medium text-xs leading-tight">
                                                {{ $loop->iteration }}. {{ $item['nama'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 px-1.5 py-0.5 rounded font-bold">Mid</span>
                                </li>
                            @empty
                                <p class="text-xs text-gray-500 italic">Tidak ada data</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Cluster 3: Kurang Laris (Red) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border-t-4 border-red-500">
                <div class="px-6 py-4 bg-red-50 dark:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Kurang Laris</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Low Demand</p>
                        </div>
                        <div class="text-4xl font-bold text-red-600">📉</div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="mb-4">
                        <p class="text-3xl font-bold text-red-600">
                            {{ count(array_filter($sortedClusters[2] ?? [], function ($item) {
        return true; })) }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Jenis Kendaraan</p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase mb-3">Top 3</p>
                        <ul class="space-y-2">
                            @forelse(array_slice($sortedClusters[2] ?? [], 0, 3) as $item)
                                @php
                                    $namaMobil = strtolower($item['nama']);
                                    $namaFile = 'default.png';
                                    if (str_contains($namaMobil, 'xpander')) { $namaFile = 'mitsubishi_xpander_utimate.png'; }
                                    elseif (str_contains($namaMobil, 'innova')) { $namaFile = 'toyota_innova_zenix.png'; }
                                    elseif (str_contains($namaMobil, 'avanza') || str_contains($namaMobil, 'veloz')) { $namaFile = 'toyota_avanza.png'; }
                                    elseif (str_contains($namaMobil, 'fortuner')) { $namaFile = 'toyota_fortuner_vrz.png'; }
                                    elseif (str_contains($namaMobil, 'hiace')) { $namaFile = 'toyota_hiace.png'; }
                                    elseif (str_contains($namaMobil, 'alphard') || str_contains($namaMobil, 'vellfire')) { $namaFile = 'toyota_alphard.png'; }
                                    elseif (str_contains($namaMobil, 'pajero')) { $namaFile = 'pajero.png'; }
                                    elseif (str_contains($namaMobil, 'bus')) {
                                        if (str_contains($namaMobil, 'big')) { $namaFile = 'big_bus.png'; } else { $namaFile = 'bus_pariwisata.png'; }
                                    }
                                    $imagePath = file_exists(public_path('images/cars/' . $namaFile)) ? asset('images/cars/' . $namaFile) : asset('images/cars/default.png');
                                @endphp
                                <li class="flex items-center justify-between text-sm py-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200">
                                            <img src="{{ $imagePath }}" alt="" class="w-full h-full object-contain">
                                        </div>
                                        <div>
                                            <div class="text-gray-900 dark:text-white font-medium text-xs leading-tight">
                                                {{ $loop->iteration }}. {{ $item['nama'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-1.5 py-0.5 rounded font-bold">Low</span>
                                </li>
                            @empty
                                <p class="text-xs text-gray-500 italic">Tidak ada data</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <!-- Detailed Results Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                <h2 class="text-white font-bold text-xl">Detail Anggota Klaster</h2>
                <p class="text-gray-300 text-sm mt-1">Klik header kolom untuk mengurutkan • Total {{ count($dataArmada) }}
                    kendaraan</p>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition" onclick="sortTable(0)">
                                <div class="flex items-center gap-2">
                                    Nama Armada
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 \
                                        2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 \
                                        1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition" onclick="sortTable(1)">
                                Frekuensi Sewa
                            </th>
                            <th scope="col" class="px-6 py-3 text-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition" onclick="sortTable(2)">
                                Total Unit
                            </th>
                            <th scope="col" class="px-6 py-3 text-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition" onclick="sortTable(3)">
                                Lepas Kunci
                            </th>
                            <th scope="col" class="px-6 py-3 text-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition" onclick="sortTable(4)">
                                Dengan Driver
                            </th>
                            <th scope="col" class="px-6 py-3 text-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition" onclick="sortTable(5)">
                                Cluster
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataArmada as $data)
                            @php
                                $clusterColors = [
                                    0 => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'badge' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200'],
                                    1 => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'badge' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200'],
                                    2 => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'badge' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200']
                                ];
                                $statusLabels = ['Laris', 'Sedang', 'Kurang Laris'];
                                $statusEmojis = ['🔥', '⚡', '📉'];
                            @endphp
                            <tr class="{{ $clusterColors[$data['klaster']]['bg'] }} hover:bg-opacity-50 transition">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $data['nama'] }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300"
                                    data-val="{{ $data['c1'] }}">
                                    <span class="inline-flex items-center gap-1 font-semibold">
                                        {{ $data['c1'] }}
                                        <span class="text-xs text-gray-500">kali</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300 font-semibold"
                                    data-val="{{ $data['c2'] }}">
                                    {{ $data['c2'] }} <span class="text-xs text-gray-500">unit</span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-blue-700 dark:text-blue-300"
                                    data-val="{{ $data['lepas_kunci'] }}">
                                    @if($data['lepas_kunci'] > 0)
                                        {{ $data['lepas_kunci'] }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-cyan-700 dark:text-cyan-300"
                                    data-val="{{ $data['driver'] }}">
                                    @if($data['driver'] > 0)
                                        {{ $data['driver'] }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $clusterColors[$data['klaster']]['badge'] }}">
                                        Klaster {{ $data['klaster'] + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 text-sm font-bold">
                                        <span class="text-lg">{{ $statusEmojis[$data['klaster']] }}</span>
                                        {{ $statusLabels[$data['klaster']] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('beranda.index') }}"
                class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 5h4"></path>
                </svg>
                Kembali ke Dashboard
            </a>
            <a href="{{ route('kmeans.reset') }}"
                class="flex items-center justify-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                Analisis Ulang
            </a>
        </div>

    </div>

    @push('scripts')
    <script>
        // Scatter Plot Chart
        var clusters = @json($sortedClusters);
        var seriesData = [];
        var colors = ['#3b82f6', '#eab308', '#ef4444'];
        var clusterNames = ['Laris (High Demand)', 'Sedang (Medium)', 'Kurang Laris (Low Demand)'];

        Object.keys(clusters).forEach(function (key, index) {
            var dataPoints = clusters[key].map(function (item) {
                return {
                    x: parseInt(item.c1),
                    y: parseInt(item.c2),
                    name: item.nama
                };
            });

            seriesData.push({
                name: clusterNames[index],
                data: dataPoints,
                color: colors[index]
            });
        });

        var options = {
            chart: {
                type: 'scatter',
                zoom: {
                    enabled: true,
                    type: 'xy'
                },
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
                }
            },
            colors: colors,
            series: seriesData,
            xaxis: {
                type: 'numeric',
                title: {
                    text: 'Frekuensi Sewa (Kali)',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                labels: {
                    style: {
                        fontSize: '12px',
                        colors: '#6b7280'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Total Unit Keluar',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                labels: {
                    style: {
                        fontSize: '12px',
                        colors: '#6b7280'
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '13px',
                fontWeight: 500
            },
            plotOptions: {
                scatter: {
                    size: 7,
                    dataLabels: {
                        enabled: false
                    }
                }
            },
            tooltip: {
                shared: false,
                theme: 'light',
                style: {
                    fontSize: '12px'
                },
                y: {
                    formatter: function (val) {
                        return val;
                    }
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4
            }
        };

        var chart = new ApexCharts(document.querySelector("#kmeansChart"), options);
        chart.render();

        // Table Sorting Function
        function sortTable(columnIndex) {
            var table = document.querySelector('table');
            var rows = Array.from(table.querySelectorAll('tbody tr'));
            var isAsc = table.dataset.sortDir !== 'asc';

            rows.sort((a, b) => {
                var aVal = a.cells[columnIndex].getAttribute('data-val') || a.cells[columnIndex].textContent;
                var bVal = b.cells[columnIndex].getAttribute('data-val') || b.cells[columnIndex].textContent;

                if (!isNaN(aVal) && !isNaN(bVal)) {
                    return isAsc ? aVal - bVal : bVal - aVal;
                }

                return isAsc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            });

            rows.forEach(row => table.querySelector('tbody').appendChild(row));
            table.dataset.sortDir = isAsc ? 'asc' : 'desc';
        }
    </script>
    @endpush

@endsection