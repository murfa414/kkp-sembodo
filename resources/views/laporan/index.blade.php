@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Hasil dan Laporan')

@section('content')

    {{-- CHECK IF ANALYSIS RESULT EXISTS --}}
    @if(!session()->has('kmeans_result'))

        {{-- NO RESULT: SHOW WARNING HERO --}}
        <x-hero-card title="Laporan Belum Tersedia" gradient="reverse">
            <x-slot:action>
                <a href="{{ route('kmeans.index') }}" class="btn btn-light px-4 py-2 fw-bold shadow-sm rounded-pill">
                    <i class="fas fa-calculator me-2"></i> Analisis
                </a>
            </x-slot:action>
        </x-hero-card>

    @else

        {{-- RESULT EXISTS: SHOW FULL DASHBOARD --}}
        @php
            // Determine car image for top performer
            $mobilJuara1 = $laris['top3'][0] ?? null;
            $gambarMobil = 'default.png';

            if ($mobilJuara1) {
                $namaMobil = strtolower($mobilJuara1['nama']);
                $namaFile = 'default.png';

                if (str_contains($namaMobil, 'xpander')) $namaFile = 'mitsubishi_xpander_utimate.png';
                elseif (str_contains($namaMobil, 'innova')) $namaFile = 'toyota_innova_zenix.png';
                elseif (str_contains($namaMobil, 'avanza') || str_contains($namaMobil, 'veloz')) $namaFile = 'avanza.png';
                elseif (str_contains($namaMobil, 'fortuner')) $namaFile = 'toyota_fortuner_vrz.png';
                elseif (str_contains($namaMobil, 'hiace')) $namaFile = 'hiace.png';
                elseif (str_contains($namaMobil, 'alphard') || str_contains($namaMobil, 'vellfire')) $namaFile = 'alphard.png';
                elseif (str_contains($namaMobil, 'pajero')) $namaFile = 'pajero.png';
                elseif (str_contains($namaMobil, 'bus')) $namaFile = 'bus.png';

                $gambarMobil = file_exists(public_path('images/cars/' . $namaFile)) ? $namaFile : 'default.png';
            }
        @endphp

        <div class="row">
            {{-- ACTION BUTTONS --}}
            <div class="col-12 mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('laporan.pdf', ['mode' => 'preview']) }}" class="btn btn-primary fw-bold px-4" target="_blank">
                        <i class="fas fa-eye me-2"></i> Preview Laporan
                    </a>
                    <a href="{{ route('laporan.pdf', ['mode' => 'download']) }}" class="btn btn-dark fw-bold px-4">
                        <i class="fas fa-file-pdf me-2"></i> Unduh PDF
                    </a>
                </div>
            </div>

            {{-- CARD 1: KURANG LARIS (RED) --}}
            <div class="col-12 col-md-6 mb-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-header text-white py-3 d-flex align-items-center justify-content-center gap-2 bg-danger rounded-top">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h5 class="m-0 fw-bold">Kurang Laris</h5>
                    </div>
                    <div class="card-body text-center p-4 p-md-5">
                        <div class="display-1 fw-bold text-dark mb-2">{{ $kurangLaris['count'] }}</div>
                        <h5 class="fw-bold text-uppercase text-secondary mb-4">JENIS MOBIL</h5>
                        <hr class="w-25 mx-auto mb-4">
                        <div class="text-start d-inline-block">
                            <p class="small text-muted mb-2 fw-bold">3 Teratas:</p>
                            <ul class="list-unstyled">
                                @forelse($kurangLaris['top3'] as $index => $item)
                                    <li class="mb-2 fw-bold text-dark">{{ $index + 1 }}. {{ ucwords(strtolower($item['nama'])) }}</li>
                                @empty
                                    <li class="text-muted fst-italic">- Data Kosong -</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: SEDANG (YELLOW) --}}
            <div class="col-12 col-md-6 mb-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-header text-white py-3 d-flex align-items-center justify-content-center gap-2 bg-warning rounded-top">
                        <i class="fas fa-check-circle text-dark"></i>
                        <h5 class="m-0 fw-bold text-dark">Sedang</h5>
                    </div>
                    <div class="card-body text-center p-4 p-md-5">
                        <div class="display-1 fw-bold text-dark mb-2">{{ $sedang['count'] }}</div>
                        <h5 class="fw-bold text-uppercase text-secondary mb-4">JENIS MOBIL</h5>
                        <hr class="w-25 mx-auto mb-4">
                        <div class="text-start d-inline-block">
                            <p class="small text-muted mb-2 fw-bold">3 Teratas:</p>
                            <ul class="list-unstyled">
                                @forelse($sedang['top3'] as $index => $item)
                                    <li class="mb-2 fw-bold text-dark">{{ $index + 1 }}. {{ ucwords(strtolower($item['nama'])) }}</li>
                                @empty
                                    <li class="text-muted fst-italic">- Data Kosong -</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 3: LARIS (BLUE) --}}
            <div class="col-12">
                <div class="card border-0 shadow">
                    <div class="card-header text-white py-3 d-flex align-items-center justify-content-center gap-2 bg-primary rounded-top">
                        <i class="fas fa-fire"></i>
                        <h5 class="m-0 fw-bold">Laris</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row align-items-center">
                            {{-- Left Column: Data --}}
                            <div class="col-12 col-md-6 text-center text-md-start ps-md-5 mb-4 mb-md-0">
                                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3 mb-2">
                                    <div class="display-1 fw-bold text-dark">{{ $laris['count'] }}</div>
                                    <h4 class="fw-bold text-uppercase text-secondary m-0">JENIS<br>MOBIL</h4>
                                </div>
                                <hr class="w-50 my-4 d-none d-md-block">
                                <hr class="w-50 mx-auto my-4 d-md-none">
                                <p class="small text-muted mb-2 fw-bold">3 Teratas:</p>
                                <ul class="list-unstyled">
                                    @forelse($laris['top3'] as $index => $item)
                                        <li class="mb-2 fw-bold text-dark fs-5">{{ $index + 1 }}. {{ ucwords(strtolower($item['nama'])) }}</li>
                                    @empty
                                        <li class="text-muted fst-italic">- Data Kosong -</li>
                                    @endforelse
                                </ul>
                            </div>

                            {{-- Right Column: Car Image --}}
                            <div class="col-12 col-md-6 text-center">
                                <img src="{{ asset('images/cars/' . $gambarMobil) }}" alt="Mobil Terlaris" 
                                    class="img-fluid transition-all"
                                    style="max-height: 250px; filter: drop-shadow(10px 10px 15px rgba(0,0,0,0.3));">
                                @if($mobilJuara1)
                                    <p class="mt-3 fw-bold text-secondary">{{ ucwords(strtolower($mobilJuara1['nama'])) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif

@endsection