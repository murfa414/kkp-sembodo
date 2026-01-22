    @extends('layouts.admin')

    @section('title', 'Analisis K-Means')
    @section('page-title', 'Analisis Kategori Unit')

    @section('content')

    {{-- Style Tambahan untuk Animasi & Gradasi --}}
    <style>
        .hover-scale { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .hover-scale:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 1rem 3rem rgba(21, 28, 49, 0.25)!important; }
        
        .bg-soft-gradient { background: linear-gradient(135deg, #eef2fa 0%, #e0eafc 100%); }
        .btn-gradient-primary { 
            background: linear-gradient(to right, #4e73df 0%, #224abe 100%); 
            border: none; 
        }
        .btn-gradient-primary:hover { 
            background: linear-gradient(to right, #224abe 0%, #4e73df 100%); 
        }
        
        /* Animasi Pulse Halus pada Ikon */
        @keyframes pulse-soft {
            0% { box-shadow: 0 0 0 0 rgba(78, 115, 223, 0.2); }
            70% { box-shadow: 0 0 0 15px rgba(78, 115, 223, 0); }
            100% { box-shadow: 0 0 0 0 rgba(78, 115, 223, 0); }
        }
        .icon-pulse { animation: pulse-soft 2s infinite; }
    </style>

    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-12 col-md-10 col-lg-7">

            {{-- CARD UTAMA --}}
            <div class="card border-0 shadow-sm mb-4 text-center rounded-5 position-relative overflow-hidden bg-white">
                
                {{-- Hiasan Garis Atas --}}
                <div class="position-absolute top-0 start-0 w-100" style="height: 6px; background: linear-gradient(90deg, #571212, #2c3a63);"></div>

                <div class="card-body p-5">
                    
                    <div class="mb-5 mt-2">
                        {{-- Ikon Besar dengan Background Soft --}}
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4 icon-pulse bg-soft-gradient" 
                            style="width: 130px; height: 130px;">
                            <i class="fas fa-chart-line fa-4x"></i>
                        </div>

                        <h3 class="fw-bolder text-dark mb-3">Analisis Kategori Unit</h3>
                        
                        <p class="text-muted mx-auto lh-lg fs-6 text-start" style="max-width: 500px;">
                            Sistem akan secara otomatis mengelompokkan unit menjadi kategori 
                            <span class="text-primary fw-bold">Laris</span> 
                            <span class="text-warning fw-bold">Sedang</span> 
                            <span class="text-danger fw-bold">Kurang Laris</span> 
                            berdasarkan jumlah penyewaan.
                        </p>
                    </div>

                    <form action="{{ route('kmeans.process') }}" method="POST">
                        @csrf
                        
                        {{-- INPUT TERSEMBUNYI (Fixed) --}}
                        <input type="hidden" name="atribut[]" value="frekuensi">
                        <input type="hidden" name="atribut[]" value="total_unit">
                        <input type="hidden" name="jumlah_klaster" value="3">

                        {{-- TOMBOL ACTION CANTIK --}}
                        <button type="submit" class="btn btn-gradient-primary btn-lg px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale w-100" style="max-width: 300px; color: white">
                            <i></i> Analisis
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>

    {{-- MODAL ERROR --}}
    @if($errors->any())
    <div class="modal fade" id="modalValidasiKmeans" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-body text-center p-5">
                    <div class="text-danger mb-3 bg-soft-danger d-inline-block p-3 rounded-circle" style="background-color: #ffeaea;">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Ops! Ada Masalah</h4>
                    <ul class="list-unstyled text-muted small mb-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new bootstrap.Modal(document.getElementById('modalValidasiKmeans')).show();
        });
    </script>
    @endif

    @endsection