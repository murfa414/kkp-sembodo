@extends('layouts.admin')

@section('title', 'Beranda')
@section('page-title', 'Sembodo Rent A Car')

@section('content')

    {{-- KONDISI 1: DATA TRANSAKSI KOSONG --}}
    @if(count($topArmada) == 0)

        <x-hero-card title="Data Transaksi Belum Tersedia">
            <x-slot:action>
                <a href="{{ route('upload.index') }}" class="btn btn-light px-4 py-2 fw-bold shadow-sm rounded-pill">
                    <i class="fas fa-file-upload me-2"></i> Impor Data Sekarang
                </a>
            </x-slot:action>
        </x-hero-card>

    {{-- KONDISI 2: DATA ADA, BELUM ANALISIS --}}
    @elseif(!session()->has('kmeans_result'))

        <x-hero-card title="Data Siap Dianalisis">
            <x-slot:action>
                <a href="{{ route('kmeans.index') }}" class="btn btn-light px-4 py-2 fw-bold shadow-sm rounded-pill">
                    <i class="fas fa-calculator me-2"></i> Analisis
                </a>
            </x-slot:action>
        </x-hero-card>

    {{-- KONDISI 3: DATA ADA & SUDAH ANALISIS --}}
    @else

        {{-- FILTER BAR --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('beranda.index') }}" class="row align-items-end g-2 g-md-3">

                    {{-- Filter Bulan --}}
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1">
                            <i class="fas fa-calendar-alt me-1"></i> Periode
                        </label>
                        <select name="bulan" class="form-select form-select-sm">
                            <option value="">Semua Bulan</option>
                            @foreach($availableMonths as $month)
                                <option value="{{ $month['value'] }}" {{ $selectedMonth == $month['value'] ? 'selected' : '' }}>
                                    {{ $month['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Brand --}}
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1">
                            <i class="fas fa-car me-1"></i> Merek
                        </label>
                        <select name="brand" class="form-select form-select-sm">
                            <option value="">Semua Merek</option>
                            @foreach($availableBrands as $brand)
                                <option value="{{ $brand }}" {{ $selectedBrand == $brand ? 'selected' : '' }}>
                                    {{ $brand }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-6 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-filter me-1"></i> <span class="d-none d-sm-inline">Terapkan</span>
                        </button>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="{{ route('beranda.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-sync-alt me-1"></i> <span class="d-none d-sm-inline">Reset</span>
                        </a>
                    </div>

                    {{-- Filter Active Badge --}}
                    <div class="col-12 col-md-2 text-md-end mt-2 mt-md-0">
                        @if($selectedMonth || $selectedBrand)
                            <span class="badge bg-info text-white">
                                <i class="fas fa-filter me-1"></i> Filter Aktif
                            </span>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- STATISTIK RINGKASAN --}}
        <div class="row mb-4 g-3">
            <div class="col-12 col-md-4">
                <x-stat-card color="primary" icon="receipt" label="Total Transaksi" :value="number_format($totalTransaksi ?? 0)" />
            </div>
            <div class="col-12 col-md-4">
                <x-stat-card color="success" icon="car" label="Total Unit Keluar Disewa" :value="number_format($totalUnit ?? 0)" />
            </div>
            <div class="col-12 col-md-4">
                <x-stat-card color="info" icon="layer-group" label="Jenis Unit" :value="count($topArmada)" />
            </div>
        </div>

        <div class="row">

            {{-- KOLOM KIRI: PERFORMA ARMADA --}}
            <div class="col-12 col-lg-5 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="mb-4">
                            <h5 class="fw-bold mb-1">Performa Unit</h5>
                            <small class="text-muted">Berdasarkan frekuensi sewa dan Total Unit Keluar.</small>
                        </div>

                        <div class="d-flex flex-column gap-3" style="max-height: 650px; overflow-y: auto; overflow-x: hidden; padding-right: 5px;">
                            @foreach($topArmada as $index => $mobil)
                                @php
                                    // Image mapping logic
                                    $namaMobil = strtolower($mobil->jenis_armada);
                                    $namaFile = 'default.png';

                                    if (str_contains($namaMobil, 'xpander')) $namaFile = 'mitsubishi_xpander_utimate.png';
                                    elseif (str_contains($namaMobil, 'innova')) $namaFile = 'toyota_innova_zenix.png';
                                    elseif (str_contains($namaMobil, 'fortuner')) $namaFile = 'toyota_fortuner_vrz.png';
                                    elseif (str_contains($namaMobil, 'alphard') || str_contains($namaMobil, 'vellfire')) $namaFile = 'toyota_alphard.png';
                                    elseif (str_contains($namaMobil, 'avanza') || str_contains($namaMobil, 'veloz')) $namaFile = 'toyota_avanza.png';
                                    elseif (str_contains($namaMobil, 'hiace')) $namaFile = 'toyota_hiace.png';
                                    elseif (str_contains($namaMobil, 'pajero')) $namaFile = 'pajero.png';
                                    elseif (str_contains($namaMobil, 'e200') || str_contains($namaMobil, 'e 250') || str_contains($namaMobil, '250')) $namaFile = 'mercedes_benz_200.jpg';
                                    elseif (str_contains($namaMobil, 'e300') || str_contains($namaMobil, 'e 300') || str_contains($namaMobil, '300')) $namaFile = 'mercedes_benz_300.png';
                                    elseif (str_contains($namaMobil, 'mercy') || str_contains($namaMobil, 'mercedes') || str_contains($namaMobil, 'sprinter')) $namaFile = 'mercedes_benz_sprinter.jpg';
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

                                    $gambar = file_exists(public_path('images/cars/' . $namaFile)) ? $namaFile : 'default.png';

                                    // Status colors
                                    $rank = $loop->index;
                                    if ($rank < 3) {
                                        $label = 'Laris';
                                        $badgeClass = 'text-white';
                                        $warnaStatus = '#4E73DF';
                                    } elseif ($rank < 7) {
                                        $label = 'Sedang';
                                        $badgeClass = 'text-dark';
                                        $warnaStatus = '#F6C23E';
                                    } else {
                                        $label = 'Kurang Laris';
                                        $badgeClass = 'text-white';
                                        $warnaStatus = '#E74A3B';
                                    }

                                    $persen = ($mobil->score / $maxScore) * 100;
                                @endphp

                                <div class="row align-items-center">
                                    <div class="col-12 mb-2">
                                        <span class="fw-bold fs-6 text-dark">
                                            <span class="text-muted me-1">{{ $loop->iteration }}.</span>
                                            {{ ucwords(strtolower($mobil->jenis_armada)) }}
                                        </span>
                                    </div>

                                    <div class="col-3 text-center">
                                        <img src="{{ asset('images/cars/' . $gambar) }}" class="img-fluid"
                                            alt="{{ $mobil->jenis_armada }}"
                                            style="filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.15)); max-height: 50px; object-fit: contain;">
                                    </div>

                                    <div class="col-9">
                                        <div class="d-flex justify-content-between mb-1 flex-wrap">
                                            <span class="small fw-bold text-secondary" style="font-size: 0.7rem;">
                                                {{ $mobil->frequency }} Transaksi • {{ $mobil->total_unit }} Unit
                                            </span>
                                            <span class="badge rounded-pill {{ $badgeClass }}"
                                                style="background-color: {{ $warnaStatus }}; font-size: 0.65rem;">
                                                {{ $label }}
                                            </span>
                                        </div>
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

            {{-- KOLOM KANAN: GRAFIK --}}
            <div class="col-12 col-lg-7">

                {{-- TREN BULANAN --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-bold mb-3">Tren Penyewaan Bulanan</h5>
                        <div id="trendChart" style="height: 250px;"></div>
                    </div>
                </div>

                {{-- PROPORSI LAYANAN --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <div class="mb-4">
                            <h5 class="fw-bold mb-1">Layanan</h5>
                            <small class="text-muted">Poin dihitung berdasarkan transaksi dan Total Unit Keluar per hari.</small>
                        </div>

                        <div class="row align-items-center mb-4">
                            <div class="col-7 col-md-7">
                                <div id="layananChart" style="height: 200px;"></div>
                            </div>
                            <div class="col-5 col-md-5">
                                <ul class="list-unstyled small">
                                    <li class="mb-3 d-flex align-items-center">
                                        <span class="d-inline-block me-2 rounded" style="width: 20px; height: 12px; background-color: #4E73DF;"></span>
                                        <span class="fw-bold">Lepas Kunci</span>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <span class="d-inline-block me-2 rounded" style="width: 20px; height: 12px; background-color: #36B9CC;"></span>
                                        <span class="fw-bold">Dengan Driver</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <hr class="mb-4">

                        {{-- TOP 5 PER LAYANAN --}}
                        <div class="row">
                            {{-- Lepas Kunci --}}
                            <div class="col-12 col-md-6 border-end-md mb-4 mb-md-0">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-2">
                                        <i class="fas fa-key text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark small">Terlaris Lepas Kunci</h6>
                                    </div>
                                </div>
                                
                                @forelse($topLepasKunci as $item)
                                    <div class="d-flex align-items-center p-2 mb-2 rounded-3 bg-light border transition-all hover-scale">
                                        <span class="badge bg-primary me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                            {{ $loop->iteration }}
                                        </span>
                                        <img src="{{ asset('images/cars/' . $item->image) }}" alt="" class="me-2 d-none d-sm-block" style="max-height: 30px; width: 40px; object-fit: contain;">
                                        <span class="fw-bold text-dark small text-truncate" style="max-width: 120px;">{{ ucwords(strtolower($item->jenis_armada)) }}</span>
                                        <span class="ms-auto fw-bold text-primary small">{{ $item->freq + $item->unit }}</span>
                                    </div>
                                @empty
                                    <p class="text-muted small text-center py-3">Tidak ada data</p>
                                @endforelse
                            </div>

                            {{-- Dengan Driver --}}
                            <div class="col-12 col-md-6 ps-md-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-info bg-opacity-10 p-2 rounded-3 me-2">
                                        <i class="fas fa-user-tie text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark small">Terlaris Dengan Driver</h6>
                                    </div>
                                </div>
                                
                                @forelse($topDriver as $item)
                                    <div class="d-flex align-items-center p-2 mb-2 rounded-3 bg-light border transition-all hover-scale">
                                        <span class="badge bg-info me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                            {{ $loop->iteration }}
                                        </span>
                                        <img src="{{ asset('images/cars/' . $item->image) }}" alt="" class="me-2 d-none d-sm-block" style="max-height: 30px; width: 40px; object-fit: contain;">
                                        <span class="fw-bold text-dark small text-truncate" style="max-width: 120px;">{{ ucwords(strtolower($item->jenis_armada)) }}</span>
                                        <span class="ms-auto fw-bold text-info small">{{ $item->freq + $item->unit }}</span>
                                    </div>
                                @empty
                                    <p class="text-muted small text-center py-3">Tidak ada data</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif

@endsection

@push('scripts')
@if(session()->has('kmeans_result') && count($topArmada) > 0)
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    // Trend Chart
    Highcharts.chart('trendChart', {
        chart: { type: 'area', style: { fontFamily: 'Nunito, sans-serif' } },
        title: { text: null },
        xAxis: {
            categories: @json($chartTrend['categories'] ?? []),
            gridLineWidth: 0,
            lineColor: 'transparent'
        },
        yAxis: { title: { text: null }, gridLineDashStyle: 'Dash' },
        tooltip: { shared: true, valueSuffix: ' Transaksi' },
        credits: { enabled: false },
        plotOptions: {
            area: { fillOpacity: 0.1, marker: { enabled: true, radius: 4 }, lineWidth: 2 }
        },
        series: [{
            name: 'Total Sewa',
            data: @json($chartTrend['data'] ?? []),
            color: '#4E73DF'
        }],
        legend: { enabled: false }
    });

    // Layanan Chart (Donut)
    Highcharts.chart('layananChart', {
        chart: { type: 'pie', style: { fontFamily: 'Nunito, sans-serif' } },
        title: { text: null },
        tooltip: { pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' },
        credits: { enabled: false },
        plotOptions: {
            pie: { innerSize: '60%', dataLabels: { enabled: false }, showInLegend: false, borderWidth: 0 }
        },
        series: [{
            name: 'Proporsi',
            data: @json($chartLayanan ?? []),
            colors: ['#4E73DF', '#36B9CC']
        }]
    });
</script>
@endif
@endpush