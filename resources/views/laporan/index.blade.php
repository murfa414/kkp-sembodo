@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Hasil dan Laporan')

@section('content')

    {{-- CEK APAKAH SUDAH ADA HASIL ANALISIS DI SESSION? --}}
    @if(!session()->has('kmeans_result'))

        {{-- JIKA BELUM ANALISIS: TAMPILKAN HERO CARD WARNING --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden"
            style="background: linear-gradient(135deg, #2c3a63 0%, #571212 100%);">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-lg-8 mb-4 mb-lg-0 text-white">
                        <h3 class="fw-bold mb-3 text-white">Laporan Belum Tersedia</h3>
                        {{-- <p class="lead mb-4 text-white" style="opacity: 0.85;">
                            Sistem belum dapat menampilkan pengelompokan armada karena proses analisis belum dijalankan.
                        </p> --}}
                        <a href="{{ route('kmeans.index') }}" class="btn btn-light px-4 py-2 fw-bold shadow-sm"
                            style="color: #000000; border-radius: 50px;">
                            <i class="fas fa-calculator me-2"></i>Analisis
                        </a>
                    </div>
                    {{-- <div class="col-lg-4 text-center d-none d-lg-block">
                        <i class="fas fa-chart-pie fa-8x" style="color: rgba(0,0,0,0.1); transform: rotate(15deg);"></i>
                    </div> --}}
                </div>
            </div>
        </div>

    @else

        {{-- JIKA SUDAH ANALISIS: TAMPILKAN DASHBOARD LENGKAP --}}
        {{-- LOGIKA UNTUK MENENTUKAN GAMBAR MOBIL JUARA 1 LARIS --}}
        @php
            // 1. Ambil Mobil Juara 1 (Index ke-0 dari top3 Laris)
            $mobilJuara1 = $laris['top3'][0] ?? null;

            // Default gambar kalau tidak ditemukan
            $gambarMobil = 'default.png';

            if ($mobilJuara1) {
                $namaMobil = strtolower($mobilJuara1['nama']);
                $namaFile = 'default.png'; // Inisialisasi nama file target

                // Cek kata kunci untuk menentukan nama file target
                if (str_contains($namaMobil, 'xpander')) {
                    $namaFile = 'mitsubishi_xpander_utimate.png';
                } elseif (str_contains($namaMobil, 'innova')) {
                    $namaFile = 'toyota_innova_zenix.png';
                } elseif (str_contains($namaMobil, 'avanza') || str_contains($namaMobil, 'veloz')) {
                    $namaFile = 'avanza.png';
                } elseif (str_contains($namaMobil, 'fortuner')) {
                    $namaFile = 'toyota_fortuner_vrz.png';
                } elseif (str_contains($namaMobil, 'hiace')) {
                    $namaFile = 'hiace.png';
                } elseif (str_contains($namaMobil, 'alphard') || str_contains($namaMobil, 'vellfire')) {
                    $namaFile = 'alphard.png';
                } elseif (str_contains($namaMobil, 'pajero')) {
                    $namaFile = 'pajero.png';
                } elseif (str_contains($namaMobil, 'bus')) {
                    $namaFile = 'bus.png';
                }

                // 2. CEK APAKAH FILE TERSEBUT BENAR-BENAR ADA DI FOLDER PUBLIC?
                // Fungsi public_path() mengarah ke folder public di server
                if (file_exists(public_path('images/cars/' . $namaFile))) {
                    $gambarMobil = $namaFile; // Kalau ada, pakai
                } else {
                    $gambarMobil = 'default.png'; // Kalau gak ada, paksa pakai default
                }
            }
        @endphp


        <div class="row">
            <div class="col-12 mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('laporan.pdf', ['mode' => 'preview']) }}" class="btn btn-primary fw-bold px-4"
                        target="_blank">
                        <i class="fas fa-eye me-2"></i> Preview Laporan
                    </a>
                    <a href="{{ route('laporan.pdf', ['mode' => 'download']) }}" class="btn btn-dark fw-bold px-4">
                        <i class="fas fa-file-pdf me-2"></i> Unduh PDF
                    </a>
                </div>
            </div>
            {{-- CARD 1: KURANG LARIS (MERAH) --}}
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-header text-white py-3 d-flex align-items-center justify-content-center gap-2"
                        style="background-color: #E74A3B; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h5 class="m-0 fw-bold">Kurang Laris</h5>
                    </div>

                    <div class="card-body text-center p-5">
                        <div class="display-1 fw-bold text-dark mb-2">{{ $kurangLaris['count'] }}</div>
                        <h5 class="fw-bold text-uppercase text-secondary mb-4">JENIS MOBIL</h5>

                        <hr class="w-25 mx-auto mb-4">

                        <div class="text-start d-inline-block">
                            <p class="small text-muted mb-2 fw-bold">3 Teratas:</p>
                            <ul class="list-unstyled">
                                @forelse($kurangLaris['top3'] as $index => $item)
                                    <li class="mb-2 fw-bold text-dark">
                                        {{ $index + 1 }}. {{ $item['nama'] }}
                                    </li>
                                @empty
                                    <li class="text-muted fst-italic">- Data Kosong -</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: SEDANG (KUNING) --}}
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-header text-white py-3 d-flex align-items-center justify-content-center gap-2"
                        style="background-color: #F6C23E; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        <i class="fas fa-check-circle text-dark"></i>
                        <h5 class="m-0 fw-bold text-dark">Sedang</h5>
                    </div>

                    <div class="card-body text-center p-5">
                        <div class="display-1 fw-bold text-dark mb-2">{{ $sedang['count'] }}</div>
                        <h5 class="fw-bold text-uppercase text-secondary mb-4">JENIS MOBIL</h5>

                        <hr class="w-25 mx-auto mb-4">

                        <div class="text-start d-inline-block">
                            <p class="small text-muted mb-2 fw-bold">3 Teratas:</p>
                            <ul class="list-unstyled">
                                @forelse($sedang['top3'] as $index => $item)
                                    <li class="mb-2 fw-bold text-dark">
                                        {{ $index + 1 }}. {{ $item['nama'] }}
                                    </li>
                                @empty
                                    <li class="text-muted fst-italic">- Data Kosong -</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 3: LARIS (BIRU) --}}
            <div class="col-12">
                <div class="card border-0 shadow">
                    <div class="card-header text-white py-3 d-flex align-items-center justify-content-center gap-2"
                        style="background-color: #4E73DF; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        <i class="fas fa-fire"></i>
                        <h5 class="m-0 fw-bold">Laris</h5>
                    </div>

                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            {{-- Kolom Kiri: Data --}}
                            <div class="col-md-6 text-center text-md-start ps-md-5">
                                <div
                                    class="d-flex align-items-center justify-content-center justify-content-md-start gap-3 mb-2">
                                    <div class="display-1 fw-bold text-dark">{{ $laris['count'] }}</div>
                                    <h4 class="fw-bold text-uppercase text-secondary m-0">JENIS<br>MOBIL</h4>
                                </div>

                                <hr class="w-50 my-4 d-none d-md-block">
                                <hr class="w-50 mx-auto my-4 d-md-none">

                                <p class="small text-muted mb-2 fw-bold">3 Teratas:</p>
                                <ul class="list-unstyled">
                                    @forelse($laris['top3'] as $index => $item)
                                        <li class="mb-2 fw-bold text-dark fs-5">
                                            {{ $index + 1 }}. {{ $item['nama'] }}
                                        </li>
                                    @empty
                                        <li class="text-muted fst-italic">- Data Kosong -</li>
                                    @endforelse
                                </ul>
                            </div>

                            {{-- Kolom Kanan: Gambar Mobil Dinamis --}}
                            <div class="col-md-6 text-center mt-4 mt-md-0">

                                {{-- GUNAKAN VARIABEL $gambarMobil HASIL LOGIKA DI ATAS --}}
                                <img src="{{ asset('images/cars/' . $gambarMobil) }}" alt="Mobil Terlaris" class="img-fluid"
                                    style="max-height: 250px; filter: drop-shadow(10px 10px 15px rgba(0,0,0,0.3)); transition: all 0.3s ease-in-out;">

                                {{-- Tampilkan Nama Mobil Juara 1 di bawah gambar --}}
                                @if($mobilJuara1)
                                    <p class="mt-3 fw-bold text-secondary">{{ $mobilJuara1['nama'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @endif

@endsection