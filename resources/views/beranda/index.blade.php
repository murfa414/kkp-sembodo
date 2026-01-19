@extends('layouts.admin')

@section('title', 'Beranda')
@section('page-title', 'Sembodo Rent A Car')

@section('content')


{{-- KONDISI 1: JIKA DATA TRANSAKSI MASIH KOSONG (DATABASE KOSONG) --}}

@if(count($topArmada) == 0)

    {{-- Card Hero Ungu --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #571212 0%, #2c3a63 100%);">
        <div class="card-body p-5">
            <div class="row align-items-center">
                
                {{-- Kolom Kiri: Teks & Tombol --}}
                <div class="col-lg-8 mb-4 mb-lg-0 text-white">
                    <h3 class="fw-bold mb-3">Data Transaksi Belum Tersedia</h3>
                    <a href="{{ route('upload.index') }}" class="btn btn-light px-4 py-2 fw-bold shadow-sm" style="color: #000000; border-radius: 50px;">
                        <i class="fas fa-file-upload me-2"></i> Impor Data Sekarang
                    </a>
                </div>

                {{-- Kolom Kanan: Ilustrasi Icon (Opsional) --}}
                {{-- <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="fas fa-chart-area fa-8x" style="color: rgba(255,255,255,0.3); transform: rotate(-10deg);"></i>
                </div> --}}

            </div>
        </div>
    </div>


{{-- KONDISI 2: DATA ADA, TAPI BELUM KLIK "PROSES K-MEANS" (SESSION KOSONG) --}}

@elseif(!session()->has('kmeans_result'))

    {{-- Card Hero Biru Muda (Suruh Analisis) --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #571212 0%, #2c3a63 100%);">
        <div class="card-body p-5">
            <div class="row align-items-center">
                
                <div class="col-lg-8 mb-4 mb-lg-0 text-white">
                    <h3 class="fw-bold mb-3 text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.1);">Data Siap Dianalisis</h3>
                    {{-- <p class="lead mb-4 text-white" style="opacity: 0.9;">
                        Data transaksi telah diunggah, namun sistem belum melakukan analisis K-Means.<br>
                        Silakan jalankan proses analisis untuk melihat Dashboard Performa Armada.
                    </p> --}}
                    <a href="{{ route('kmeans.index') }}" class="btn btn-light px-4 py-2 fw-bold shadow-sm" style="color: #000000; border-radius: 50px;">
                        <i class="fas fa-calculator me-2"></i>Analisis
                    </a>
                </div>

                {{-- <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="fas fa-chart-line fa-8x" style="color: rgba(255,255,255,0.3); transform: rotate(10deg);"></i>
                </div> --}}

            </div>
        </div>
    </div>


{{-- KONDISI 3: DATA ADA & SUDAH ANALISIS (TAMPILKAN DASHBOARD UTUH) --}}

@else

    <div class="row">
        
        {{-- KOLOM KIRI: PERFORMA ARMADA (TOP 10 BY SKOR) --}}
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    {{-- Judul & Subjudul Tumpuk ke Bawah --}}
                    <div class="mb-4">
                        <h5 class="fw-bold mb-1" style="text-decoration: underline; text-decoration-color: #333;">Performa Unit</h5>
                        <small class="text-muted">Performa berdasarkan rata-rata frekuensi sewa dan total unit.</small>
                    </div>

                    {{--Tambahkan 'overflow-x: hidden' untuk hilangkan scroll samping --}}
                    <div class="d-flex flex-column gap-3" style="max-height: 650px; overflow-y: auto; overflow-x: hidden; padding-right: 10px;">
                        @foreach($topArmada as $index => $mobil)
                            @php
                                // --- LOGIKA GAMBAR DINAMIS ---
                                $namaMobil = strtolower($mobil->jenis_armada);
                                $namaFile = 'default.png'; 
                                
                                if (str_contains($namaMobil, 'xpander')) $namaFile = 'mitsubishi_xpander_utimate.png';
                                elseif (str_contains($namaMobil, 'innova')) $namaFile = 'toyota_innova_zenix.png';
                                elseif (str_contains($namaMobil, 'fortuner')) $namaFile = 'toyota_fortuner_vrz.png';
                                elseif (str_contains($namaMobil, 'alphard') || str_contains($namaMobil, 'vellfire')) $namaFile = 'alphard.png';
                                elseif (str_contains($namaMobil, 'avanza') || str_contains($namaMobil, 'veloz')) $namaFile = 'avanza.png';
                                elseif (str_contains($namaMobil, 'hiace')) $namaFile = 'hiace.png';
                                elseif (str_contains($namaMobil, 'pajero')) $namaFile = 'pajero.png';
                                elseif (str_contains($namaMobil, 'mercy') || str_contains($namaMobil, 'mercedes')) $namaFile = 'mercy.png';
                                elseif (str_contains($namaMobil, 'pariwisata')) $namaFile = 'bus_pariwisata.png';
                                elseif (str_contains($namaMobil, 'big')) $namaFile = 'big_bus.png';

                                if (file_exists(public_path('images/cars/' . $namaFile))) {
                                    $gambar = $namaFile;
                                } else {
                                    $gambar = 'default.png';
                                }
                                
                                // --- LOGIKA WARNA & LABEL (LARIS, SEDANG, KURANG LARIS) ---
                                $rank = $loop->index;
                                
                                if ($rank < 3) { 
                                    // Rank 1-3: Laris (Biru)
                                    $label = 'Laris';
                                    $badgeClass = 'text-white'; 
                                    $warnaStatus = '#4E73DF'; 
                                } elseif ($rank < 7) { 
                                    // Rank 4-7: Sedang (Kuning)
                                    $label = 'Sedang';
                                    $badgeClass = 'text-dark'; 
                                    $warnaStatus = '#F6C23E';
                                } else { 
                                    // Rank 8-10: Kurang Laris (Merah)
                                    $label = 'Kurang Laris';
                                    $badgeClass = 'text-white'; 
                                    $warnaStatus = '#E74A3B';
                                }

                                // Hitung Persentase Progress Bar BERDASARKAN SKOR
                                $persen = ($mobil->score / $maxScore) * 100;
                            @endphp

                            {{-- ITEM LIST MOBIL --}}
                            <div class="row align-items-center">
                                {{-- 1. Nama & Nomor Urut --}}
                                <div class="col-12 mb-2">
                                    <span class="fw-bold fs-6 text-dark">
                                        <span class="text-muted me-1">{{ $loop->iteration }}.</span> 
                                        {{ $mobil->jenis_armada }}
                                    </span>
                                </div>
                                
                                {{-- 2. Gambar Mobil (Kiri) --}}
                                <div class="col-3 text-center">
                                    <img src="{{ asset('images/cars/' . $gambar) }}" 
                                         class="img-fluid" 
                                         alt="{{ $mobil->jenis_armada }}"
                                         style="filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.15)); max-height: 50px; object-fit: contain;">
                                </div>

                                {{-- 3. Progress Bar & Stats (Kanan) --}}
                                <div class="col-9">
                                    <div class="d-flex justify-content-between mb-1">
                                        {{-- Tampilkan Detail: Frekuensi & Unit --}}
                                        <span class="small fw-bold text-secondary" style="font-size: 0.75rem;">
                                            {{ $mobil->frequency }} Transaksi <span class="mx-1">•</span> {{ $mobil->total_unit }} Unit Disewa
                                        </span>
                                        
                                        {{-- BADGE STATUS BARU --}}
                                        <span class="badge rounded-pill {{ $badgeClass }}" style="background-color: {{ $warnaStatus }}; font-size: 0.7rem;">
                                            {{ $label }}
                                        </span>
                                    </div>
                                    
                                    {{-- PROGRESS BAR DENGAN WARNA DINAMIS --}}
                                    <div class="progress" style="height: 6px; border-radius: 10px; background-color: #eaecf4;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $persen }}%; background-color: {{ $warnaStatus }};" 
                                             aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            
                            @if(!$loop->last)
                                <hr class="my-0 opacity-25">
                            @endif

                        @endforeach
                    </div>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: GRAFIK TREN & DONUT --}}
        <div class="col-lg-7">
            
            {{-- CARD 1: TREN PENYEWAAN BULANAN --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="text-decoration: underline; text-decoration-color: #333;">Tren Penyewaan Bulanan</h5>
                    <div id="trendChart" style="height: 250px;"></div>
                </div>
            </div>

            {{-- CARD 2: PROPORSI LAYANAN + TOP 5 --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    {{-- Judul & Subjudul Rapat --}}
                    <div class="mb-4">
                        <h5 class="fw-bold mb-1" style="text-decoration: underline; text-decoration-color: #333;">Proporsi Layanan</h5>
                        <small class="text-muted">Poin dihitung berdasarkan jumlah transaksi dan total unit yang disewa per hari.</small>
                    </div>
                    
                    {{-- BAGIAN ATAS: GRAFIK DONUT --}}
                    <div class="row align-items-center mb-4">
                        <div class="col-md-7">
                            <div id="layananChart" style="height: 200px;"></div>
                        </div>
                        <div class="col-md-5">
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex align-items-center">
                                    <span style="width: 30px; height: 15px; background-color: #4E73DF; display: inline-block; border-radius: 3px; margin-right: 10px;"></span>
                                    <div><h6 class="m-0 fw-bold small">Lepas Kunci</h6></div>
                                </li>
                                <li class="d-flex align-items-center">
                                    <span style="width: 30px; height: 15px; background-color: #36B9CC; display: inline-block; border-radius: 3px; margin-right: 10px;"></span>
                                    <div><h6 class="m-0 fw-bold small">Dengan Driver</h6></div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <hr class="mb-4">

                    {{-- BAGIAN BAWAH: LIST TOP 5 PER LAYANAN --}}
                    <div class="row">
                        {{-- Kolom Kiri: Top 5 Lepas Kunci --}}
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold mb-3" style="color: #4E73DF;"><i class="fas fa-key me-2 text-secondary"></i><span class="text-secondary">5 teratas</span> Lepas Kunci</h6>
                            <ul class="list-group list-group-flush">
                                @forelse($topLepasKunci as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="small text-dark fw-bold">{{ $loop->iteration }}. {{ $item->jenis_armada }}</span>
                                        <span class="badge bg-light text-primary border rounded-pill" style="font-size: 0.7rem;">{{ $item->freq + $item->unit }} Poin</span>
                                    </li>
                                @empty
                                    <li class="small text-muted fst-italic">Data tidak tersedia</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Kolom Kanan: Top 5 Driver --}}
                        <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                            <h6 class="fw-bold mb-3" style="color: #36B9CC;"><i class="fas fa-user-tie me-2 text-secondary"></i> <span class="text-secondary">5 teratas</span> Dengan Driver</h6>
                            <ul class="list-group list-group-flush">
                                @forelse($topDriver as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="small text-dark fw-bold">{{ $loop->iteration }}. {{ $item->jenis_armada }}</span>
                                        <span class="badge bg-light text-info border rounded-pill" style="font-size: 0.7rem; color: #36B9CC !important;">{{ $item->freq + $item->unit }} Poin</span>
                                    </li>
                                @empty
                                    <li class="small text-muted fst-italic">Data tidak tersedia</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT HIGHCHARTS (HANYA DILOAD JIKA DASHBOARD MUNCUL) --}}
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <script>
        // --- 1. CONFIG TREND CHART (GARIS) ---
        Highcharts.chart('trendChart', {
            chart: { type: 'area', style: { fontFamily: 'Nunito, sans-serif' } },
            title: { text: null },
            xAxis: { 
                categories: @json($chartTrend['categories'] ?? []),
                gridLineWidth: 0,
                lineColor: 'transparent'
            },
            yAxis: { 
                title: { text: null }, 
                gridLineDashStyle: 'Dash' 
            },
            tooltip: { shared: true, valueSuffix: ' Transaksi' },
            credits: { enabled: false },
            plotOptions: {
                area: {
                    fillOpacity: 0.1,
                    marker: { enabled: true, radius: 4 },
                    lineWidth: 2
                }
            },
            series: [{
                name: 'Total Sewa',
                data: @json($chartTrend['data'] ?? []),
                color: '#4E73DF' 
            }],
            legend: { enabled: false }
        });

        // --- 2. CONFIG LAYANAN CHART (DONUT) ---
        Highcharts.chart('layananChart', {
            chart: { type: 'pie', style: { fontFamily: 'Nunito, sans-serif' } },
            title: { text: null },
            tooltip: { pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' },
            credits: { enabled: false },
            plotOptions: {
                pie: {
                    innerSize: '60%', 
                    dataLabels: { enabled: false }, 
                    showInLegend: false,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Proporsi',
                data: @json($chartLayanan ?? []),
                colors: ['#4E73DF', '#36B9CC'] 
            }]
        });
    </script>

@endif

@endsection