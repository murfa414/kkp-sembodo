<!DOCTYPE html>
<html>

<head>
    <title>Laporan Hasil Analisis K-Means - Sembodo Rent A Car</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin-top: 3cm;
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
            color: #374151;
            /* text-gray-700 */
        }

        /* Tailwind Compatibility Layer for Dompdf */
        .fixed {
            position: fixed;
        }

        .absolute {
            position: absolute;
        }

        .top-0 {
            top: 0;
        }

        .left-0 {
            left: 0;
        }

        .right-0 {
            right: 0;
        }

        .bottom-0 {
            bottom: 0;
        }

        .w-full {
            width: 100%;
        }

        .h-full {
            height: 100%;
        }

        .bg-gray-900 {
            background-color: #111827;
        }

        .text-white {
            color: #ffffff;
        }

        .p-8 {
            padding: 2rem;
        }

        .px-8 {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .text-2xl {
            font-size: 1.5rem;
            line-height: 2rem;
        }

        .font-bold {
            font-weight: 700;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .tracking-widest {
            letter-spacing: 0.1em;
        }

        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .text-xs {
            font-size: 0.75rem;
            line-height: 1rem;
        }

        .text-gray-400 {
            color: #9ca3af;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .text-gray-900 {
            color: #111827;
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .mb-8 {
            margin-bottom: 2rem;
        }

        .border-b-2 {
            border-bottom-width: 2px;
        }

        .border-blue-500 {
            border-color: #3b82f6;
        }

        .border-blue-600 {
            border-color: #2563eb;
        }

        .pb-2 {
            padding-bottom: 0.5rem;
        }

        .bg-white {
            background-color: #ffffff;
        }

        .border {
            border-width: 1px;
        }

        .border-gray-200 {
            border-color: #e5e7eb;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .bg-blue-600 {
            background-color: #2563eb;
        }

        .bg-yellow-500 {
            background-color: #eab308;
        }

        .bg-red-600 {
            background-color: #dc2626;
        }

        .p-4 {
            padding: 1rem;
        }

        .flex {
            display: flex;
        }

        /* Note: Dompdf has limited flex support, consider table for layout */
        .justify-between {
            justify-content: space-between;
        }

        .items-center {
            align-items: center;
        }

        .grid {
            display: grid;
        }

        /* Dompdf doesn't support grid well, fallback to table */
        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        /* Custom Table Utilities simulating Tailwind */
        .w-table {
            width: 100%;
            border-collapse: collapse;
        }

        .th-cell {
            text-align: left;
            padding: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .td-cell {
            padding: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .bg-gray-100 {
            background-color: #f3f4f6;
        }

        .w-8 {
            width: 2rem;
        }

        .h-8 {
            height: 2rem;
        }

        .h-24 {
            height: 6rem;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .text-center {
            text-align: center;
        }

        .leading-8 {
            line-height: 2rem;
        }

        .leading-loose {
            line-height: 2;
        }

        .object-contain {
            object-fit: contain;
        }

        .h-10 {
            height: 2.5rem;
        }

        .w-auto {
            width: auto;
        }

        .rounded {
            border-radius: 0.25rem;
        }

        /* Table Layout Helpers (since Flex/Grid is flaky in PDF) */
        .table-layout {
            display: table;
            width: 100%;
        }

        .table-row {
            display: table-row;
        }

        .table-cell {
            display: table-cell;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 h-24 bg-white border-b-2 border-blue-600 leading-loose">
        <div class="table-layout h-full px-8">
            <div class="table-row">
                <div class="table-cell w-1/2">
                    <div class="text-2xl font-bold uppercase tracking-widest text-blue-600">Sembodo Rent</div>
                </div>
                <div class="table-cell w-1/2 text-right">
                    <div class="text-sm text-gray-600">Laporan Analisis K-Means</div>
                </div>
            </div>
        </div>
    </header>

    <!-- Footer -->
    <footer
        class="fixed bottom-0 left-0 right-0 h-16 text-center border-t border-gray-200 text-xs text-gray-400 leading-loose">
        <div style="padding-top: 1rem;">
            Dicetak otomatis oleh Sistem Mining Data Sembodo Rent pada {{ date('d F Y, H:i') }}
        </div>
    </footer>

    <!-- Content -->
    <div class="mt-8">
        <h1 class="text-2xl font-bold text-gray-900 border-b-2 border-blue-500 pb-2 inline-block mb-2">Ringkasan Hasil
            Analisis</h1>
        <p class="text-sm text-gray-600 mb-8">
            Berikut adalah hasil pengelompokan armada berdasarkan frekuensi sewa dan total unit menggunakan algoritma
            K-Means Clustering.
        </p>
    </div>

    <!-- CARD 1: LARIS (BLUE) -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm mb-6">
        <div class="bg-blue-600 p-4 text-white font-bold text-sm uppercase tracking-widest">
            Paling Laris (High Demand)
        </div>
        <div class="p-4">
            <div class="table-layout mb-4">
                <div class="table-cell">
                    <span class="text-2xl font-bold text-gray-900">{{ $laris['count'] }}</span>
                    <div class="text-xs text-uppercase text-gray-600 tracking-widest">Total Armada</div>
                </div>
                <div class="table-cell text-right">
                    <span class="text-xs text-uppercase text-gray-600 tracking-widest">Kategori</span>
                    <div class="font-bold text-blue-600">Utama</div>
                </div>
            </div>

            <table class="w-table">
                <thead>
                    <tr>
                        <th class="th-cell" width="10%">Rank</th>
                        <th class="th-cell" width="20%">Visual</th>
                        <th class="th-cell">Nama Armada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laris['top3'] as $index => $item)
                        @php
                            $namaMobil = strtolower($item['nama']);
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
                                $namaFile = 'pajero.png';
                            } elseif (str_contains($namaMobil, 'bus')) {
                                if (str_contains($namaMobil, 'big')) {
                                    $namaFile = 'big_bus.png';
                                } else {
                                    $namaFile = 'bus_pariwisata.png';
                                }
                            }

                            $path = public_path('images/cars/' . $namaFile);
                            if (!file_exists($path)) {
                                $path = public_path('images/cars/default.png');
                            }

                            $base64 = '';
                            if (file_exists($path)) {
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            }
                        @endphp
                        <tr>
                            <td class="td-cell">
                                <span
                                    class="bg-gray-100 text-gray-600 font-bold w-8 h-8 rounded-full text-center leading-8 inline-block text-xs">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="td-cell">
                                @if($base64)
                                    <img src="{{ $base64 }}" class="h-10 w-auto object-contain rounded border border-gray-200"
                                        alt="Car">
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="td-cell">
                                <span class="font-bold text-gray-900">{{ $item['nama'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="td-cell text-center text-gray-400 py-4">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CARD 2: SEDANG (YELLOW) -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm mb-6">
        <div class="bg-yellow-500 p-4 text-white font-bold text-sm uppercase tracking-widest">
            Sedang (Medium Demand)
        </div>
        <div class="p-4">
            <div class="table-layout mb-4">
                <div class="table-cell">
                    <span class="text-2xl font-bold text-gray-900">{{ $sedang['count'] }}</span>
                    <div class="text-xs text-uppercase text-gray-600 tracking-widest">Total Armada</div>
                </div>
            </div>

            <table class="w-table">
                <thead>
                    <tr>
                        <th class="th-cell" width="10%">Rank</th>
                        <th class="th-cell" width="20%">Visual</th>
                        <th class="th-cell">Nama Armada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sedang['top3'] as $index => $item)
                        @php
                            $namaMobil = strtolower($item['nama']);
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
                                $namaFile = 'pajero.png';
                            } elseif (str_contains($namaMobil, 'bus')) {
                                if (str_contains($namaMobil, 'big')) {
                                    $namaFile = 'big_bus.png';
                                } else {
                                    $namaFile = 'bus_pariwisata.png';
                                }
                            }

                            $path = public_path('images/cars/' . $namaFile);
                            if (!file_exists($path)) {
                                $path = public_path('images/cars/default.png');
                            }

                            $base64 = '';
                            if (file_exists($path)) {
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            }
                        @endphp
                        <tr>
                            <td class="td-cell">
                                <span
                                    class="bg-gray-100 text-gray-600 font-bold w-8 h-8 rounded-full text-center leading-8 inline-block text-xs">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="td-cell">
                                @if($base64)
                                    <img src="{{ $base64 }}" class="h-10 w-auto object-contain rounded border border-gray-200"
                                        alt="Car">
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="td-cell">
                                <span class="font-bold text-gray-900">{{ $item['nama'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="td-cell text-center text-gray-400 py-4">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CARD 3: KURANG LARIS (RED) -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm mb-6">
        <div class="bg-red-600 p-4 text-white font-bold text-sm uppercase tracking-widest">
            Kurang Laris (Low Demand)
        </div>
        <div class="p-4">
            <div class="table-layout mb-4">
                <div class="table-cell">
                    <span class="text-2xl font-bold text-gray-900">{{ $kurangLaris['count'] }}</span>
                    <div class="text-xs text-uppercase text-gray-600 tracking-widest">Total Armada</div>
                </div>
            </div>

            <table class="w-table">
                <thead>
                    <tr>
                        <th class="th-cell" width="10%">Rank</th>
                        <th class="th-cell" width="20%">Visual</th>
                        <th class="th-cell">Nama Armada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kurangLaris['top3'] as $index => $item)
                        @php
                            $namaMobil = strtolower($item['nama']);
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
                                $namaFile = 'pajero.png';
                            } elseif (str_contains($namaMobil, 'bus')) {
                                if (str_contains($namaMobil, 'big')) {
                                    $namaFile = 'big_bus.png';
                                } else {
                                    $namaFile = 'bus_pariwisata.png';
                                }
                            }

                            $path = public_path('images/cars/' . $namaFile);
                            if (!file_exists($path)) {
                                $path = public_path('images/cars/default.png');
                            }

                            $base64 = '';
                            if (file_exists($path)) {
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            }
                        @endphp
                        <tr>
                            <td class="td-cell">
                                <span
                                    class="bg-gray-100 text-gray-600 font-bold w-8 h-8 rounded-full text-center leading-8 inline-block text-xs">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="td-cell">
                                @if($base64)
                                    <img src="{{ $base64 }}" class="h-10 w-auto object-contain rounded border border-gray-200"
                                        alt="Car">
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="td-cell">
                                <span class="font-bold text-gray-900">{{ $item['nama'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="td-cell text-center text-gray-400 py-4">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>