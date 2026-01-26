@extends('layouts.admin')

@section('title', 'Import Data')
@section('page-title', 'Unggah File Data Transaksi')

@section('content')

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    {{-- ALERT ERROR VALIDASI --}}
    @if($errors->any())
        <x-alert type="danger" :errors="$errors->all()" />
    @endif

    {{-- MAIN UPLOAD CARD --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            @if(!$dataExists)
                {{-- FORM UPLOAD AWAL --}}
                <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="upload-area p-4 p-md-5 text-center rounded-4 transition-all"
                        style="border: 2px dashed #4E73DF; background-color: #F8F9FC; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#eef2ff'" 
                        onmouseout="this.style.backgroundColor='#F8F9FC'"
                        onclick="document.getElementById('fileInput').click()">

                        <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary"></i>
                        <h5 class="fw-bold text-dark">Unggah File</h5>
                        <p class="text-muted small mb-0">Format yang didukung: .XLSX, .CSV (Maks. 10MB)</p>

                        <input type="file" name="file" id="fileInput" class="d-none"
                            onchange="validateFile(this, 'uploadForm')">
                    </div>
                </form>

            @else
                {{-- TAMPILAN FILE SUDAH DIUPLOAD --}}
                <div class="row">
                    {{-- CARD 1: STATUS UPLOAD TERAKHIR --}}
                    <div class="col-12 col-lg-5 mb-4">
                        <div class="card border-0 shadow-sm h-100 overflow-hidden">
                            <div class="card-body p-4 position-relative">
                                <div class="position-absolute top-0 end-0 p-3 opacity-10 d-none d-md-block">
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
                                    <button class="btn btn-light text-danger fw-bold py-2 border"
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
                                    @csrf 
                                    <input type="hidden" name="replace_data" value="1">
                                    <input type="file" name="file" id="replaceFileInput" class="d-none"
                                        onchange="validateFile(this, 'replaceForm')">
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: RIWAYAT FILE UPLOAD --}}
                    <div class="col-12 col-lg-7 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h6 class="m-0 fw-bold text-dark">
                                    <i class="fas fa-history me-2 text-secondary"></i>Riwayat Upload
                                </h6>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold"
                                    onclick="bukaModal('modalKonfirmasiHapus')">
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
                                                            <div class="bg-light p-2 rounded me-3 d-none d-sm-block">
                                                                <i class="fas fa-file-csv text-primary"></i>
                                                            </div>
                                                            <span class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                                                {{ $file->source_file ?? 'Data Lama' }}
                                                            </span>
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
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold text-dark m-0">Tampilan Data Transaksi</h6>
        </div>
        <div class="card-body p-0">
            @if($dataExists)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0" id="uploadTable">
                        <thead class="table-dark sticky-top" style="z-index: 10;">
                            <tr class="text-nowrap text-center align-middle">
                                <th class="py-3" style="cursor: pointer;" onclick="sortUploadTable(0)">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <span>Tanggal Sewa</span>
                                        <i class="fas fa-sort text-secondary"></i>
                                    </div>
                                </th>
                                <th class="text-start" style="cursor: pointer;" onclick="sortUploadTable(1)">
                                    <div class="d-flex align-items-center justify-content-start gap-2">
                                        <span>Nama Penyewa</span>
                                        <i class="fas fa-sort text-secondary"></i>
                                    </div>
                                </th>
                                <th style="cursor: pointer;" onclick="sortUploadTable(2)">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <span>Unit</span>
                                        <i class="fas fa-sort text-secondary"></i>
                                    </div>
                                </th>
                                <th style="cursor: pointer;" onclick="sortUploadTable(3)">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <span>Layanan</span>
                                        <i class="fas fa-sort text-secondary"></i>
                                    </div>
                                </th>
                                <th style="cursor: pointer;" onclick="sortUploadTable(4)">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <span>Frekuensi</span>
                                        <i class="fas fa-sort text-secondary"></i>
                                    </div>
                                </th>
                                <th class="d-none d-md-table-cell" style="cursor: pointer;" onclick="sortUploadTable(5)">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <span>Total Unit Keluar</span>
                                        <i class="fas fa-sort text-secondary"></i>
                                    </div>
                                </th>
                                <th style="cursor: pointer;" onclick="sortUploadTable(6)">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <span>Jumlah</span>
                                        <i class="fas fa-sort text-secondary"></i>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewData as $dt)
                                <tr>
                                    <td class="text-center" data-val="{{ $dt->tanggal_sewa }}">{{ \Carbon\Carbon::parse($dt->tanggal_sewa)->format('d/m/Y') }}</td>
                                    <td class="fw-bold text-justify" style="text-align: justify; min-width: 250px;">{{ ucwords(strtolower($dt->nama_penyewa)) }}</td>
                                    <td class="text-nowrap">{{ ucwords(strtolower($dt->jenis_armada)) }}</td>
                                    <td class="text-nowrap text-center fs-5" data-val="{{ $dt->layanan }}">
                                        @if($dt->layanan == 'Lepas Kunci')
                                            <span class="badge bg-primary rounded-pill px-3" style="min-width: 140px;">Lepas Kunci</span>
                                        @else
                                            <span class="badge bg-success rounded-pill px-3" style="min-width: 140px;">Dengan Driver</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold" data-val="{{ $dt->frekuensi }}">{{ $dt->frekuensi }}</td>
                                    <td class="text-center fw-bold d-none d-md-table-cell" data-val="{{ $dt->total_unit }}">{{ $dt->total_unit }}</td>
                                    <td class="text-center fw-bold" data-val="{{ $dt->jumlah_sewa }}">{{ $dt->jumlah_sewa }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-3">
                        {{ $previewData->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="d-flex flex-column justify-content-center align-items-center py-5" style="min-height: 300px;">
                    <div class="bg-light rounded-circle p-4 mb-3">
                        <i class="fas fa-database fa-3x text-secondary opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark mt-2">Data Belum Tersedia</h5>
                    <p class="text-muted text-center px-3">
                        Silakan unggah file Excel/CSV pada panel di atas<br class="d-none d-md-inline">
                        untuk melihat pratinjau data transaksi.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL: KONFIRMASI HAPUS SEMUA --}}
    <x-modal id="modalKonfirmasiHapus" icon="exclamation-triangle" color="warning" title="Yakin Ingin Menghapus?">
        Data transaksi yang sudah diupload akan hilang permanen.<br>
        Anda harus mengupload ulang jika ingin menganalisisnya.
        <x-slot:actions>
            <button type="button" class="btn btn-light border fw-bold px-4" onclick="tutupModal('modalKonfirmasiHapus')">Batal</button>
            <button type="button" class="btn btn-danger fw-bold px-4" onclick="document.getElementById('formHapus').submit()">Hapus Data</button>
        </x-slot:actions>
    </x-modal>

    {{-- MODAL: ERROR EKSTENSI --}}
    <x-modal id="modalErrorEkstensi" icon="times-circle" color="danger" title="Format File Salah!">
        Sistem hanya menerima file dengan ekstensi<br>
        <span class="badge bg-light text-dark border">.XLSX</span>,
        <span class="badge bg-light text-dark border">.XLS</span>, atau
        <span class="badge bg-light text-dark border">.CSV</span>
        <x-slot:actions>
            <button type="button" class="btn btn-light border fw-bold px-4" onclick="tutupModal('modalErrorEkstensi')">Tutup</button>
        </x-slot:actions>
    </x-modal>

    {{-- MODAL: ERROR TEMPLATE --}}
    <x-modal id="modalErrorTemplate" icon="file-circle-question" color="danger" title="Isi File Tidak Sesuai!">
        Sistem mendeteksi struktur tabel yang salah.<br>
        Pastikan Anda mengunggah file <span class="fw-bold text-dark">Dataset Transaksi Sembodo</span> yang valid.
        <x-slot:actions>
            <button type="button" class="btn btn-light border fw-bold px-4" onclick="tutupModal('modalErrorTemplate')">Tutup</button>
        </x-slot:actions>
    </x-modal>

    {{-- MODAL: ERROR SESSION --}}
    <x-modal id="modalErrorSession" icon="exclamation-circle" color="warning" title="Data Belum Ditemukan">
        {!! session('error') !!}
        <x-slot:actions>
            <button type="button" class="btn btn-light border fw-bold px-4" onclick="tutupModal('modalErrorSession')">Tutup</button>
        </x-slot:actions>
    </x-modal>

    {{-- MODAL: HAPUS FILE SATUAN --}}
    <x-modal id="modalHapusFileSatuan" icon="trash-alt" color="warning" title="Hapus File Ini?">
        Data dari file <span id="namaFileHapus" class="fw-bold text-dark"></span> akan dihapus permanen.
        <x-slot:actions>
            <button type="button" class="btn btn-light border fw-bold px-4" onclick="tutupModal('modalHapusFileSatuan')">Batal</button>
            <button type="button" class="btn btn-danger fw-bold px-4" onclick="prosesHapusFile()">Ya, Hapus</button>
        </x-slot:actions>
    </x-modal>

@endsection

@push('scripts')
<script>
    // Modal Functions
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
            new bootstrap.Modal(modalEl).hide();
        }
    }

    // File Validation
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

    // Delete Specific File
    let fileToDelete = '';

    function hapusFile(filename) {
        fileToDelete = filename;
        document.getElementById('namaFileHapus').textContent = filename;
        bukaModal('modalHapusFileSatuan');
    }

    function prosesHapusFile() {
        if(fileToDelete) {
            document.getElementById('inputFilename').value = fileToDelete;
            document.getElementById('formHapusFile').submit();
        }
    }

    // On Page Load
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('error_template'))
            bukaModal('modalErrorTemplate');
        @endif

        @if(session('error'))
            bukaModal('modalErrorSession');
        @endif
    });

    // Table Sorting Function
    function sortUploadTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("uploadTable");
        if (!table) return;
        
        switching = true;
        dir = "asc"; 
        
        while (switching) {
            switching = false;
            rows = table.rows;
            
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];
                
                if (!x || !y) continue;
                
                // Get sort value (use data-val if available, otherwise use text)
                var xVal = x.getAttribute('data-val') !== null ? x.getAttribute('data-val') : x.innerText.toLowerCase().trim();
                var yVal = y.getAttribute('data-val') !== null ? y.getAttribute('data-val') : y.innerText.toLowerCase().trim();
                
                // Check if values are numeric
                var xNum = parseFloat(xVal);
                var yNum = parseFloat(yVal);
                
                if (!isNaN(xNum) && !isNaN(yNum)) {
                    xVal = xNum;
                    yVal = yNum;
                }

                if (dir == "asc") {
                    if (xVal > yVal) { shouldSwitch = true; break; }
                } else if (dir == "desc") {
                    if (xVal < yVal) { shouldSwitch = true; break; }
                }
            }
            
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount++;      
            } else {
                if (switchcount == 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    }
</script>
@endpush