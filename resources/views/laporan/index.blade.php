@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Hasil Laporan')

@section('content')

    {{-- CEK APAKAH SUDAH ADA HASIL ANALISIS DI SESSION? --}}
    @if(!session()->has('kmeans_result'))

        {{-- JIKA BELUM ANALISIS: TAMPILKAN HERO CARD WARNING --}}
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl shadow-xl overflow-hidden border border-gray-700">
            <div class="p-8 md:p-12 text-center md:text-left">
                <div class="md:flex md:items-center md:gap-8">
                    <div class="md:flex-1">
                        <h3 class="text-3xl font-bold text-white mb-4">Laporan Belum Tersedia</h3>
                        <p class="text-lg text-gray-300 mb-8 leading-relaxed">
                            Sistem belum dapat menampilkan pengelompokan armada karena proses analisis belum dijalankan. 
                            Silakan lakukan analisis K-Means terlebih dahulu untuk menghasilkan laporan.
                        </p>
                        <a href="{{ route('kmeans.index') }}" 
                           class="inline-flex items-center gap-2 bg-white text-gray-900 px-6 py-3 rounded-full font-bold hover:bg-gray-100 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Jalankan Analisis K-Means
                        </a>
                    </div>
                    <div class="hidden md:block">
                        <svg class="w-48 h-48 text-gray-700 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0 1 1 0 002 0zm2-4a1 1 0 110-2 1 1 0 010 2zm3 4a1 1 0 10-2 0 1 1 0 002 0zm2-4a1 1 0 110-2 1 1 0 010 2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
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
                     // Cek bus pariwisata atau big bus
                     if (str_contains($namaMobil, 'big')) {
                         $namaFile = 'big_bus.png';
                     } else {
                         $namaFile = 'bus_pariwisata.png';
                     }
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


        <div class="space-y-6">
            
            <div class="flex justify-end">
                <a href="{{ route('laporan.pdf') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold shadow-md transition" target="_blank">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l4 4a1 1 0 01.586 1.414V19a2 2 0 01-2 2z"></path></svg>
                    Unduh Laporan PDF
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- CARD 1: KURANG LARIS (MERAH) --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden flex flex-col h-full transform transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="bg-red-500 py-3 flex items-center justify-center gap-2">
                        <span class="text-2xl text-white">📉</span>
                        <h5 class="text-white font-bold text-lg">Kurang Laris</h5>
                    </div>
                    
                    <div class="p-8 flex-grow text-center">
                        <div class="text-6xl font-extrabold text-gray-800 dark:text-white mb-2">{{ $kurangLaris['count'] }}</div>
                        <h5 class="text-sm font-bold text-gray-500 tracking-wider uppercase mb-6">JENIS MOBIL</h5>
                        
                        <div class="w-16 h-1 bg-red-200 mx-auto mb-6 rounded-full"></div>
                        
                        <div class="text-left inline-block w-full max-w-xs bg-red-50 dark:bg-red-900/10 p-4 rounded-lg">
                            <p class="text-xs text-red-600 dark:text-red-400 font-bold mb-3 uppercase tracking-wide">3 Teratas:</p>
                            <ul class="space-y-2">
                                @forelse($kurangLaris['top3'] as $index => $item)
                                    <li class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-start gap-2">
                                        <span class="text-red-500 font-bold">{{ $index + 1 }}.</span>
                                        <span>{{ $item['nama'] }}</span>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500 italic text-center py-2">- Data Kosong -</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: SEDANG (KUNING) --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden flex flex-col h-full transform transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="bg-yellow-400 py-3 flex items-center justify-center gap-2">
                        <span class="text-2xl text-white">⚡</span>
                        <h5 class="text-white font-bold text-lg">Sedang</h5>
                    </div>
                    
                    <div class="p-8 flex-grow text-center">
                        <div class="text-6xl font-extrabold text-gray-800 dark:text-white mb-2">{{ $sedang['count'] }}</div>
                        <h5 class="text-sm font-bold text-gray-500 tracking-wider uppercase mb-6">JENIS MOBIL</h5>
                        
                        <div class="w-16 h-1 bg-yellow-200 mx-auto mb-6 rounded-full"></div>
                        
                        <div class="text-left inline-block w-full max-w-xs bg-yellow-50 dark:bg-yellow-900/10 p-4 rounded-lg">
                            <p class="text-xs text-yellow-600 dark:text-yellow-400 font-bold mb-3 uppercase tracking-wide">3 Teratas:</p>
                            <ul class="space-y-2">
                                @forelse($sedang['top3'] as $index => $item)
                                    <li class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-start gap-2">
                                        <span class="text-yellow-500 font-bold">{{ $index + 1 }}.</span>
                                        <span>{{ $item['nama'] }}</span>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500 italic text-center py-2">- Data Kosong -</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- CARD 3: LARIS (BIRU) - FULL WIDTH --}}
                <div class="md:col-span-2 bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl shadow-xl overflow-hidden text-white relative">
                    <!-- Overlay Pattern -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full transform translate-x-1/2 -translate-y-1/2 blur-2xl"></div>
                    
                    <div class="p-8 md:p-10 relative z-10">
                        <div class="grid md:grid-cols-2 gap-8 items-center">
                            
                            {{-- Kolom Kiri: Data --}}
                            <div class="text-center md:text-left">
                                <div class="inline-flex items-center gap-2 bg-blue-500/30 px-4 py-1.5 rounded-full mb-6 border border-blue-400/30">
                                    <span class="text-xl">🔥</span>
                                    <span class="font-bold text-blue-100 uppercase text-sm tracking-wide">Paling Laris (High Demand)</span>
                                </div>
                                
                                <div class="flex items-center justify-center md:justify-start gap-6 mb-8">
                                    <div class="text-8xl font-black text-white leading-none tracking-tighter">{{ $laris['count'] }}</div>
                                    <div class="text-left">
                                        <span class="block text-2xl font-bold text-blue-100">JENIS</span>
                                        <span class="block text-lg text-blue-200">MOBIL</span>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-900/30 rounded-xl p-6 backdrop-blur-sm border border-blue-500/30">
                                    <p class="text-xs text-blue-200 font-bold mb-4 uppercase tracking-wider">3 Armada Teratas:</p>
                                    <ul class="space-y-3">
                                        @forelse($laris['top3'] as $index => $item)
                                            <li class="flex items-center gap-3">
                                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white text-blue-700 text-xs font-bold shadow-sm">
                                                    {{ $index + 1 }}
                                                </span>
                                                <span class="font-semibold text-lg">{{ $item['nama'] }}</span>
                                            </li>
                                        @empty
                                            <li class="text-blue-200 italic">- Data Kosong -</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
    
                            {{-- Kolom Kanan: Gambar Mobil Dinamis --}}
                            <div class="relative flex flex-col items-center justify-center mt-6 md:mt-0">
                                <div class="relative z-10 w-full max-w-md transform transition-transform hover:scale-105 duration-500">
                                    <img src="{{ asset('images/cars/' . $gambarMobil) }}" 
                                         alt="Mobil Terlaris" 
                                         class="w-full h-auto drop-shadow-2xl filter"
                                         style="filter: drop-shadow(0 15px 15px rgba(0,0,0,0.4));">
                                </div>
                                
                                {{-- Efek Platform di bawah mobil --}}
                                <div class="absolute bottom-0 w-3/4 h-8 bg-black opacity-30 rounded-[100%] blur-xl transform translate-y-2"></div>
                                
                                {{-- Tampilkan Nama Mobil Juara 1 --}}
                                @if($mobilJuara1)
                                    <div class="mt-8 bg-white/10 backdrop-blur-md px-6 py-2 rounded-full border border-white/20">
                                        <p class="font-bold text-blue-50 tracking-wide">{{ $mobilJuara1['nama'] }}</p>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    @endif

@endsection