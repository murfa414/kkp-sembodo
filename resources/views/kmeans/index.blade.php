@extends('layouts.admin')

@section('title', 'Analisis K-Means')
@section('page-title', 'Analisis Kategori Unit')

@section('content')

<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-12 col-md-10 col-lg-7">

        {{-- MAIN CARD --}}
        <div class="card border-0 shadow-sm mb-4 text-center rounded-5 position-relative overflow-hidden bg-white">
            
            {{-- Top Gradient Line --}}
            <div class="position-absolute top-0 start-0 w-100 bg-gradient-primary" style="height: 6px;"></div>

            <div class="card-body p-4 p-md-5">
                
                <div class="mb-5 mt-2">
                    {{-- Icon with Pulse Animation --}}
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4 icon-pulse bg-soft-gradient" 
                        style="width: 130px; height: 130px;">
                        <i class="fas fa-chart-line fa-4x text-primary"></i>
                    </div>

                    <h3 class="fw-bolder text-dark mb-3">Analisis Kategori Unit</h3>
                    
                    <p class="text-muted mx-auto lh-lg fs-6 text-start text-md-center px-2" style="max-width: 500px;">
                        Sistem akan secara otomatis mengelompokkan unit menjadi kategori 
                        <span class="text-primary fw-bold">Laris</span>, 
                        <span class="text-warning fw-bold">Sedang</span>, dan 
                        <span class="text-danger fw-bold">Kurang Laris</span> 
                        berdasarkan jumlah penyewaan.
                    </p>
                </div>

                <form action="{{ route('kmeans.process') }}" method="POST">
                    @csrf
                    
                    {{-- Hidden Inputs --}}
                    <input type="hidden" name="atribut[]" value="frekuensi">
                    <input type="hidden" name="atribut[]" value="total_unit">
                    <input type="hidden" name="jumlah_klaster" value="3">

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-gradient-primary btn-lg px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale w-100" style="max-width: 300px;">
                        <i class="fas fa-play-circle me-2"></i> Analisis
                    </button>
                </form>

            </div>
        </div>

    </div>
</div>

{{-- ERROR MODAL --}}
@if($errors->any())
    <x-modal id="modalValidasiKmeans" icon="exclamation-triangle" color="danger" title="Ops! Ada Masalah">
        <ul class="list-unstyled text-muted small mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <x-slot:actions>
            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
        </x-slot:actions>
    </x-modal>
@endif

@endsection

@push('scripts')
@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new bootstrap.Modal(document.getElementById('modalValidasiKmeans')).show();
    });
</script>
@endif
@endpush