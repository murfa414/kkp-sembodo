@extends('layouts.admin')

@section('title', 'Import Data')
@section('page-title', 'UNGGAH FILE DATA TRANSAKSI')

@section('content')

{{-- 1. ALERT SUKSES (Manual Close) --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" id="alertSuccess">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" onclick="document.getElementById('alertSuccess').remove()"></button>
    </div>
@endif

{{-- 3. ALERT ERROR VALIDASI (Manual Close) --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" id="alertValidation">
        <div class="d-flex align-items-center">
            <i class="fas fa-times-circle me-2 fa-lg"></i>
            <div>
                <strong>Gagal Mengunggah!</strong>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" onclick="document.getElementById('alertValidation').remove()"></button>
    </div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        
        @if(!$dataExists)
            {{-- FORM UPLOAD AWAL --}}
            <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="upload-area p-5 text-center" 
                     style="border: 2px dashed #4E73DF; background-color: #F8F9FC; border-radius: 15px; cursor: pointer; transition: 0.3s;"
                     onmouseover="this.style.backgroundColor='#eef2ff'"
                     onmouseout="this.style.backgroundColor='#F8F9FC'"
                     onclick="document.getElementById('fileInput').click()">
                    
                    <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color: #4E73DF;"></i>
                    <h5 class="fw-bold text-dark">Klik di sini untuk mengunggah file</h5>
                    <p class="text-muted small mb-0">Format yang didukung: .XLSX, .CSV (Maks. 10MB)</p>
                    
                    <input type="file" name="file" id="fileInput" class="d-none" onchange="validateFile(this, 'uploadForm')">
                </div>
            </form>

        @else
            {{-- TAMPILAN FILE SUDAH DIUPLOAD --}}
            <div class="p-4" style="border: 2px dashed #4E73DF; background-color: #F8F9FC; border-radius: 15px;">
                <div class="d-flex align-items-center">
                    <div class="me-4 text-center">
                        <i class="fas fa-file-excel fa-4x text-success"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="fw-bold text-dark mb-0 me-2">File Selesai Diunggah</h5>
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        </div>
                        <p class="fw-bold text-primary mb-2">
                            {{ session('nama_file_aktual', 'DATASET TRANSAKSI SEMBODO.xlsx') }}
                        </p>
                        <div class="progress" style="height: 10px; width: 100%; max-width: 400px; border-radius: 5px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%;"></div>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            Ukuran: {{ session('ukuran_file_aktual', 'Data Tersimpan') }}
                        </small>
                    </div>
                </div>

                <div class="mt-4 border-top pt-3 d-flex gap-2">
                    <button class="btn btn-light btn-sm fw-bold px-3 shadow-sm border text-dark" 
                            onclick="document.getElementById('hiddenReupload').click()">
                        Ubah File
                    </button>
                    <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" id="reuploadForm">
                        @csrf
                        <input type="file" name="file" id="hiddenReupload" class="d-none" onchange="validateFile(this, 'reuploadForm')">
                    </form>
                    <button type="button" class="btn btn-danger btn-sm border-0 fw-bold px-3 text-white" 
                            onclick="bukaModal('modalKonfirmasiHapus')">
                        Hapus Data
                    </button>
                    <form action="{{ route('upload.reset') }}" method="POST" id="formHapus" class="d-none">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- TABEL PREVIEW --}}
<div class="card border-0 shadow-sm" style="min-height: 400px;">
    <div class="card-body">
        @if($dataExists)
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-primary"><i class="fas fa-table me-2"></i> Pratinjau Data Transaksi (5 Teratas)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal Sewa</th>
                            <th>Nama Penyewa</th>
                            <th>Armada</th>
                            <th>Layanan</th>
                            <th class="text-center">Jumlah Sewa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewData as $dt)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($dt->tanggal_sewa)->format('d/m/Y') }}</td>
                            <td>{{ $dt->nama_penyewa }}</td>
                            <td>{{ $dt->jenis_armada }}</td>
                            <td class="text-start align-middle">
                                @if($dt->layanan == 'Lepas Kunci')
                                    <span class="badge rounded-pill px-3 py-2 fw-normal shadow-sm bg-primary">Lepas Kunci</span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2 fw-normal shadow-sm bg-info text-dark">Dengan Driver</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold">{{ $dt->jumlah_sewa }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-muted small fst-italic mt-2">
                    *Hanya menampilkan 5 data terbaru dari basis data.
                </div>
            </div>
        @else
            <div class="d-flex flex-column justify-content-center align-items-center h-100 py-5">
                <i class="fas fa-table fa-4x mb-3 text-secondary" style="opacity: 0.3;"></i>
                <h5 class="fw-bold text-secondary">Pratinjau Data Belum Tersedia</h5>
                <p class="text-muted">Silakan unggah file di panel atas untuk melihat isi data transaksi.</p>
            </div>
        @endif
    </div>
</div>

{{-- MODAL 1: KONFIRMASI HAPUS --}}
<div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center py-5">
                <div class="mb-3 text-warning">
                    <i class="fas fa-exclamation-triangle fa-4x"></i>
                </div>
                <h5 class="fw-bold mb-2">Yakin Ingin Menghapus?</h5>
                <p class="text-muted">Data transaksi yang sudah diupload akan hilang permanen.<br>Anda harus mengupload ulang jika ingin menganalisisnya.</p>
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-light border fw-bold px-4" onclick="tutupModal('modalKonfirmasiHapus')">Batal</button>
                    <button type="button" class="btn btn-danger fw-bold px-4" onclick="document.getElementById('formHapus').submit()">Hapus Data</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: ERROR EKSTENSI --}}
<div class="modal fade" id="modalErrorEkstensi" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" 
                    onclick="tutupModal('modalErrorEkstensi')" style="z-index: 1056; cursor: pointer;"></button>
            <div class="modal-body text-center pt-5 pb-4">
                <div class="mb-3 text-danger">
                    <i class="fas fa-times-circle fa-5x"></i>
                </div>
                <h4 class="fw-bold mb-2 text-danger">Format File Salah!</h4>
                <p class="text-muted px-4">
                    Sistem hanya menerima file dengan ekstensi <br> 
                    <span class="badge bg-light text-dark border">.XLSX</span>, 
                    <span class="badge bg-light text-dark border">.XLS</span>, atau 
                    <span class="badge bg-light text-dark border">.CSV</span>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 3: ERROR TEMPLATE --}}
<div class="modal fade" id="modalErrorTemplate" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" 
                    onclick="tutupModal('modalErrorTemplate')" style="z-index: 1056; cursor: pointer;"></button>
            <div class="modal-body text-center py-5">
                <div class="mb-3 text-danger">
                    <i class="fas fa-file-circle-question fa-5x"></i>
                </div>
                <h4 class="fw-bold mb-2 text-danger">Isi File Tidak Sesuai!</h4>
                <p class="text-muted px-4 mb-0">
                    Sistem mendeteksi struktur tabel yang salah.<br>
                    Pastikan Anda mengunggah file <span class="fw-bold text-dark">Dataset Transaksi Sembodo</span> yang valid.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 4: ERROR SESSION (PENGGANTI SWEETALERT) --}}
{{-- Desain disamakan persis dengan Modal Error Ekstensi --}}
<div class="modal fade" id="modalErrorSession" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg position-relative">
            {{-- Tombol Close X Manual --}}
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" 
                    onclick="tutupModal('modalErrorSession')" style="z-index: 1056; cursor: pointer;"></button>
            
            <div class="modal-body text-center pt-5 pb-4">
                {{-- Icon --}}
                <div class="mb-3 text-warning"> {{-- Saya pakai warna kuning/orange (warning) biar beda dikit dari error file --}}
                    <i class="fas fa-exclamation-circle fa-5x"></i>
                </div>
                {{-- Judul --}}
                <h4 class="fw-bold mb-2 text-dark">Data Belum Ditemukan</h4>
                {{-- Pesan --}}
                <div class="text-muted px-4">
                     {!! session('error') !!}
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Tidak perlu load SweetAlert lagi jika tidak dipakai di tempat lain --}}
<script>
    // FUNGSI BUKA MODAL MANUAL
    function bukaModal(idModal) {
        var myModal = new bootstrap.Modal(document.getElementById(idModal));
        myModal.show();
    }

    // FUNGSI TUTUP MODAL MANUAL
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

    // FUNGSI VALIDASI FILE
    function validateFile(input, formId) {
        const fileName = input.value;
        const allowedExtensions = /(\.xlsx|\.xls|\.csv)$/i; 
        if (!allowedExtensions.exec(fileName)) {
            bukaModal('modalErrorEkstensi');
            input.value = '';
            return false;
        } else {
            document.getElementById(formId).submit();
        }
    }

    // LISTENER SAAT HALAMAN LOAD
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Cek Error Template
        @if(session('error_template'))
            bukaModal('modalErrorTemplate');
        @endif

        // 2. Cek Error Umum (Sekarang Pakai Bootstrap Modal)
        @if(session('error'))
            bukaModal('modalErrorSession');
        @endif
    });
</script>

@endsection