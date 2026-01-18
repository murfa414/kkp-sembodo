@extends('layouts.admin')

@section('title', 'Analisis K-Means')
@section('page-title', 'KONFIGURASI KLASTERISASI')

@section('content')

<style>
    /* Hilangkan tombol panah di input number Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    /* Hilangkan tombol panah di Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<div class="row">
    <div class="col-12">

        {{-- FORM KONFIGURASI --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                
                <form action="{{ route('kmeans.process') }}" method="POST" autocomplete="off">
                    @csrf
                    
                    <div class="row mb-4">
                        {{-- KOLOM KIRI: PILIH ATRIBUT --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilih Kriteria Analisis</label>
                            
                            <div class="border rounded p-3" style="background-color: #fff;">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <span class="text-muted"><i class="fas fa-list-ul me-2"></i> Kriteria tersedia:</span>
                                </div>
                                
                                {{-- CHECKBOX 1: FREKUENSI (Value harus 'frekuensi') --}}
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="atribut[]" value="frekuensi" id="attr1"
                                        {{ (is_array(old('atribut')) && in_array('frekuensi', old('atribut'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="attr1">
                                        Jumlah Sewa
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">(Berdasarkan jumlah transaksi unit)</small>
                                    </label>
                                </div>
                                
                                {{-- CHECKBOX 2: TOTAL UNIT (Value harus 'total_unit') --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="atribut[]" value="total_unit" id="attr2"
                                        {{ (is_array(old('atribut')) && in_array('total_unit', old('atribut'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="attr2">
                                        Jumlah Sewa Unit
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">(Berdasarkan Jumlah Sewa unit keluar)</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: INPUT KLASTER --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Masukkan Jumlah Kategori</label>
                            
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-layer-group text-muted"></i></span>
                                <input type="number" 
                                    name="jumlah_klaster" 
                                    class="form-control" 
                                    placeholder="Contoh: 3" 
                                    value="{{ old('jumlah_klaster') == 0 ? '' : old('jumlah_klaster') }}"> 
                            </div>
                            <div class="form-text text-muted fst-italic mt-2">
                                Disarankan 3 klaster (Laris, Sedang, Kurang Laris).
                            </div>
                        </div>
                    </div>

                    {{-- TOMBOL SUBMIT --}}
                    <button type="submit" class="btn w-100 py-3 fw-bold text-white shadow-sm" 
                            style="background-color: #4E73DF; border-radius: 8px; font-size: 1.1rem;">
                        Analisis
                    </button>

                </form>
            </div>
        </div>

        {{-- AREA KOSONG UNTUK GRAFIK (PLACEHOLDER) --}}
        <div class="card border-0 shadow-sm" style="min-height: 300px;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                
                <div class="mb-3 position-relative">
                    <i class="fas fa-table fa-5x text-secondary opacity-25"></i>
                    <i class="fas fa-times-circle position-absolute bottom-0 end-0 fa-2x text-secondary border border-white rounded-circle bg-white"></i>
                </div>
                
                <h4 class="fw-bold text-dark mt-3">Hasil Analisis Belum Tersedia</h4>
                <p class="text-muted" style="max-width: 500px;">
                    Silakan jalankan analisis K-Means di panel atas untuk melihat grafik diagram tebar dan hasil pengelompokan.
                </p>

            </div>
        </div>

    </div>
</div>


{{-- MODAL ERROR VALIDASI (Pop Up Otomatis jika error) --}}
@if($errors->any())
<div class="modal fade" id="modalValidasiKmeans" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg position-relative">
            
            {{-- Tombol Close Manual --}}
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" 
                    onclick="tutupModal('modalValidasiKmeans')" 
                    style="z-index: 1056; cursor: pointer;"></button>

            <div class="modal-body text-center py-5">
                <div class="mb-3 text-danger">
                    <i class="fas fa-exclamation-circle fa-5x"></i>
                </div>
                
                <h4 class="fw-bold mb-3 text-danger">Gagal Memproses</h4>
                
                <div class="text-muted px-4 text-start d-inline-block">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li class="mb-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        bukaModal('modalValidasiKmeans');
    });

    function bukaModal(idModal) {
        var myModal = new bootstrap.Modal(document.getElementById(idModal));
        myModal.show();
    }

    function tutupModal(idModal) {
        var modalEl = document.getElementById(idModal);
        var modalInstance = bootstrap.Modal.getInstance(modalEl);
        
        if (modalInstance) {
            modalInstance.hide();
        } else {
            var myModal = new bootstrap.Modal(modalEl);
            myModal.hide();
        }
    }
</script>
@endif

@endsection