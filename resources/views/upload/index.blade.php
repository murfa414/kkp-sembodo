@extends('layouts.admin')

@section('title', 'Import Data')
@section('page-title', 'Unggah File Data Transaksi')

<style>
    .badge-lepas-kunci,
    .badge-dengan-driver {
        display: inline-block;
        /* supaya ukuran sesuai konten */
        white-space: nowrap;
        /* supaya teks tidak pecah baris */
        padding: 6px 14px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        color: #fff !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        max-width: 100%;
        /* pastikan badge bisa menyusut */
        overflow: hidden;
        text-overflow: ellipsis;
        /* jika teks sangat panjang, tambahkan "..." */
    }

    .badge-lepas-kunci {
        background-color: #0B5ED7;
    }

    .badge-dengan-driver {
        background-color: #146C43;
    }
</style>



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
                        onmouseover="this.style.backgroundColor='#eef2ff'" onmouseout="this.style.backgroundColor='#F8F9FC'"
                        onclick="document.getElementById('fileInput').click()">

                        <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color: #4E73DF;"></i>
                        <h5 class="fw-bold text-dark">Unggah File</h5>
                        <p class="text-muted small mb-0">Format yang didukung: .XLSX, .CSV (Maks. 10MB)</p>

                        <input type="file" name="file" id="fileInput" class="d-none"
                            onchange="validateFile(this, 'uploadForm')">
                    </div>
                </form>

            @else
                {{-- TAMPILAN FILE SUDAH DIUPLOAD --}}
                <div class="row">
                    <div class="col-md-5 mb-4">
                        {{-- CARD 1: STATUS UPLOAD TERAKHIR --}}
                        <div class="card border-0 shadow-sm h-100 overflow-hidden">
                            <div class="card-body p-4 position-relative">
                                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                                    <i class="fas fa-cloud-upload-alt fa-5x text-primary"></i>
                                </div>
                                
                                <h6 class="fw-bold text-secondary text-uppercase small mb-3">Status Terakhir</h6>
                                
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                                        <i class="fas fa-file-excel fa-2x text-success"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">{{ session('nama_file_aktual', 'Data Tersimpan') }}</h5>
                                        @if(session('import_mode'))
                                            <span class="badge {{ session('import_mode') == 'replace' ? 'bg-warning text-dark' : 'bg-info text-white' }} rounded-pill px-3">
                                                {{ session('import_mode') == 'replace' ? 'Mode: Ganti Data' : 'Mode: Tambah Data' }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-secondary border rounded-pill px-3">Data Siap Dianalisis</span>
                                        @endif
                                    </div>
                                </div>

                                <hr class="border-light-subtle my-3">

                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary fw-bold py-2 shadow-sm"
                                        onclick="document.getElementById('appendFileInput').click()">
                                        <i class="fas fa-plus-circle me-2"></i> Tambah File Baru
                                    </button>
                                    <button class="btn btn-light text-danger fw-bold py-2 border hover-danger"
                                        onclick="document.getElementById('replaceFileInput').click()">
                                        <i class="fas fa-sync-alt me-2"></i> Ganti Semua Data
                                    </button>
                                </div>

                                {{-- Hidden Forms --}}
                                <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data"
                                    id="appendForm" class="d-inline">
                                    @csrf
                                    <input type="file" name="file" id="appendFileInput" class="d-none"
                                        onchange="validateFile(this, 'appendForm')">
                                </form>
                                <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data"
                                    id="replaceForm" class="d-inline">
                                    @csrf <input type="hidden" name="replace_data" value="1">
                                    <input type="file" name="file" id="replaceFileInput" class="d-none"
                                        onchange="validateFile(this, 'replaceForm')">
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7 mb-4">
                        {{-- CARD 2: RIWAYAT FILE UPLOAD --}}
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-dark"><i class="fas fa-history me-2 text-secondary"></i>Riwayat Upload</h6>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold"
                                    onclick="bukaModal('modalKonfirmasiHapus')" style="font-size: 0.75rem;">
                                    <i class="fas fa-trash me-1"></i> Reset Sistem
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                    <table class="table align-middle mb-0 table-hover">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th class="ps-4 text-secondary small text-uppercase">Nama File</th>
                                                <th class="text-center text-secondary small text-uppercase">Total Data</th>
                                                <th class="text-end pe-4 text-secondary small text-uppercase">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($uploadedFiles as $file)
                                                <tr>
                                                    <td class="ps-4 py-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-light p-2 rounded me-3">
                                                                <i class="fas fa-file-csv text-primary"></i>
                                                            </div>
                                                            <span class="fw-bold text-dark">{{ $file->source_file ?? 'Data Lama' }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark border rounded-pill px-3">{{ $file->total }} Baris</span>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        @if($file->source_file)
                                                            <button type="button" class="btn btn-light text-danger btn-sm rounded-circle shadow-sm border"
                                                                title="Hapus file ini"
                                                                style="width: 32px; height: 32px;"
                                                                onclick="hapusFile('{{ $file->source_file }}')">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @else
                                                            <small class="text-muted">-</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-5 text-muted">
                                                        <i class="fas fa-folder-open fa-2x mb-2 opacity-25"></i>
                                                        <p class="small mb-0">Belum ada riwayat file.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 py-3">
                                <form action="{{ route('upload.reset') }}" method="POST" id="formHapus" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FORM HAPUS FILE SPESIFIK --}}
                <form action="{{ route('upload.delete_file') }}" method="POST" id="formHapusFile" class="d-none">
                    @csrf @method('DELETE')
                    <input type="hidden" name="filename" id="inputFilename">
                </form>
            @endif
        </div>
    </div>

    {{-- TABEL PREVIEW --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="min-height: 400px;">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold text-dark m-0">Tampilan Data Transaksi</h6>
        </div>
        <div class="card-body p-0">
            @if($dataExists)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr class="text-nowrap text-center align-middle">
                                <th class="py-3">Tanggal Sewa</th>
                                <th class="text-start">Nama Penyewa</th>
                                <th>Unit</th>
                                <th>Layanan</th>
                                <th>Frekuensi</th>
                                <th>Total Unit Keluar</th>
                                <th>Jumlah Sewa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewData as $dt)
                                <tr>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($dt->tanggal_sewa)->format('d/m/Y') }}</td>
                                    <td class="fw-bold">{{ $dt->nama_penyewa }}</td>
                                    <td class="text-nowrap">{{ $dt->jenis_armada }}</td>
                                    <td class="text-nowrap text-center fs-5">
                                        @if($dt->layanan == 'Lepas Kunci')
                                            <span class="badge bg-primary rounded-pill px-3">Lepas Kunci</span>
                                        @else
                                            <span class="badge bg-success rounded-pill px-3">Dengan Driver</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold">{{ $dt->frekuensi }}</td>
                                    <td class="text-center fw-bold">{{ $dt->total_unit }}</td>
                                    <td class="text-center fw-bold">{{ $dt->jumlah_sewa }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-3">
                        {{ $previewData->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="d-flex flex-column justify-content-center align-items-center py-5" style="height: 300px;">
                    <div class="bg-light rounded-circle p-4 mb-3">
                        <i class="fas fa-database fa-3x text-secondary opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark mt-2">Data Belum Tersedia</h5>
                    <p class="text-muted text-center max-w-md">Silakan unggah file Excel/CSV pada panel di atas<br>untuk melihat pratinjau data transaksi.</p>
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
                    <p class="text-muted">Data transaksi yang sudah diupload akan hilang permanen.<br>Anda harus mengupload
                        ulang jika ingin menganalisisnya.</p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light border fw-bold px-4"
                            onclick="tutupModal('modalKonfirmasiHapus')">Batal</button>
                        <button type="button" class="btn btn-danger fw-bold px-4"
                            onclick="document.getElementById('formHapus').submit()">Hapus Data</button>
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
                        Pastikan Anda mengunggah file <span class="fw-bold text-dark">Dataset Transaksi Sembodo</span> yang
                        valid.
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
                    <div class="mb-3 text-warning"> {{-- Saya pakai warna kuning/orange (warning) biar beda dikit dari error
                        file --}}
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


    {{-- MODAL 5: KONFIRMASI HAPUS FILE SATUAN --}}
    <div class="modal fade" id="modalHapusFileSatuan" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-3 text-warning">
                        <i class="fas fa-trash-alt fa-4x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Hapus File Ini?</h5>
                    <p class="text-muted px-4">
                        Data dari file <span id="namaFileHapus" class="fw-bold text-dark"></span> akan dihapus permanen.
                    </p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light border fw-bold px-4"
                            onclick="tutupModal('modalHapusFileSatuan')">Batal</button>
                        <button type="button" class="btn btn-danger fw-bold px-4"
                            onclick="prosesHapusFile()">Ya, Hapus</button>
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

        // FUNGSI TRIGGER HAPUS FILE SPESIFIK (MODAL)
        let fileToDelete = '';

        function hapusFile(filename) {
            fileToDelete = filename;
            document.getElementById('namaFileHapus').textContent = filename;
            bukaModal('modalHapusFileSatuan');
        }

        // FUNGSI EKSEKUSI HAPUS
        function prosesHapusFile() {
             if(fileToDelete) {
                document.getElementById('inputFilename').value = fileToDelete;
                document.getElementById('formHapusFile').submit();
             }
        }

        // LISTENER SAAT HALAMAN LOAD
        document.addEventListener("DOMContentLoaded", function () {

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