@extends('layouts.admin')

@section('title', 'Analisis K-Means')@section('page-title', 'Analisis Data Transaksi')@section('content')<!-- Main Analysis Card -->
    <div class="max-w-2xl mx-auto mt-20">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-10 text-center">
                <h2 class="text-3xl font-bold text-white mb-2">Siap Melakukan Analisis?</h2>
                <p class="text-blue-100 text-lg">Sistem akan mengelompokkan mobil berdasarkan tingkat penyewaan secara otomatis.</p>
            </div>

            <div class="p-10 text-center">
                <div class="mb-8 flex justify-center">
                    <div class="w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center animate-pulse">
                        <svg class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                </div>

                <p class="text-gray-600 dark:text-gray-400 mb-10 max-w-lg mx-auto leading-relaxed text-base">
                    Klik tombol di bawah ini untuk memulai proses analisis. <br>
                    Hasil pengelompokan (Laris, Sedang, Kurang Laris) akan langsung ditampilkan setelah proses selesai.
                </p>

                <form action="{{ route('kmeans.process') }}" method="POST">
                    @csrf
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-12 rounded-full transition duration-300 transform hover:scale-105 shadow-xl flex items-center justify-center gap-3 mx-auto text-lg"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Lakukan Analisis
                    </button>

                    <!-- Hidden inputs for controller compatibility if needed later -->
                    <input type="hidden" name="k" value="3">
                    <input type="hidden" name="max_iterations" value="100">
                    <input type="hidden" name="convergence_threshold" value="0.0001">
                </form>
            </div>
        </div>

        <div class="text-center mt-8">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Mode analisis otomatis diaktifkan untuk kemudahan penggunaan.
            </p>
        </div>
    </div>

@endsection