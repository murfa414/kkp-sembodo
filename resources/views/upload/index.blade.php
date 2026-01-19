@extends('layouts.admin')

@section('title', 'Import Data')
@section('page-title', 'Unggah File Data Transaksi')

@section('content')

    {{-- 1. ALERT SUKSES (Manual Close) --}}
    @if(session('success'))
        <div id="alertSuccess"
            class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded flex justify-between items-start shadow-sm animate-fade-in-down">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
            <button type="button" class="text-green-500 hover:text-green-700"
                onclick="document.getElementById('alertSuccess').remove()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    {{-- 3. ALERT ERROR VALIDASI (Manual Close) --}}
    @if($errors->any())
        <div id="alertValidation"
            class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded flex justify-between items-start shadow-sm animate-fade-in-down">
            <div class="flex gap-3">
                <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-red-800 font-bold mb-1">Gagal Mengunggah!</h3>
                    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="text-red-500 hover:text-red-700"
                onclick="document.getElementById('alertValidation').remove()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 overflow-hidden">
        <div class="p-6">

            @if(!$dataExists)
                {{-- FORM UPLOAD AWAL --}}
                <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="relative border-2 border-dashed border-blue-400 dark:border-blue-600 bg-blue-50 dark:bg-blue-900/10 rounded-xl p-10 text-center cursor-pointer transition-all duration-300 hover:bg-blue-100 dark:hover:bg-blue-900/20 hover:border-blue-600 dark:hover:border-blue-500 group"
                        onclick="document.getElementById('fileInput').click()"
                        ondragover="event.preventDefault(); this.classList.add('bg-blue-100', 'border-blue-600');"
                        ondragleave="event.preventDefault(); this.classList.remove('bg-blue-100', 'border-blue-600');"
                        ondrop="event.preventDefault(); this.classList.remove('bg-blue-100', 'border-blue-600'); document.getElementById('fileInput').files = event.dataTransfer.files; validateFile(document.getElementById('fileInput'), 'uploadForm');">
                        <div class="mb-4 transform group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-16 h-16 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                        </div>
                        <h5 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Klik atau Tarik File ke Sini</h5>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Format yang didukung: <span
                                class="font-mono font-semibold text-blue-600 dark:text-blue-400">.XLSX, .CSV</span> (Maks. 10MB)
                        </p>

                        <input type="file" name="file" id="fileInput" class="hidden"
                            onchange="validateFile(this, 'uploadForm')">
                    </div>
                </form>

            @else
                {{-- TAMPILAN FILE SUDAH DIUPLOAD --}}
                <div
                    class="relative border-2 border-dashed border-green-500/50 bg-green-50/50 dark:bg-green-900/10 rounded-xl p-6">
                    <div class="flex items-center gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h5 class="text-lg font-bold text-gray-900 dark:text-white truncate">File Selesai Diunggah</h5>
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="font-medium text-gray-700 dark:text-gray-300 mb-2 truncate">
                                {{ session('nama_file_aktual', 'DATASET TRANSAKSI SEMBODO.xlsx') }}
                            </p>
                            <div class="w-full max-w-sm bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-2">
                                <div class="bg-green-600 h-2.5 rounded-full" style="width: 100%"></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Ukuran: {{ session('ukuran_file_aktual', 'Data Tersimpan') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="document.getElementById('hiddenReupload').click()"
                                class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition">
                                Ganti File
                            </button>
                            <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data"
                                id="reuploadForm">
                                @csrf
                                <input type="file" name="file" id="hiddenReupload" class="hidden"
                                    onchange="validateFile(this, 'reuploadForm')">
                            </form>

                            <button onclick="bukaModal('modalKonfirmasiHapus')"
                                class="px-4 py-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                                Hapus Data
                            </button>
                            <form action="{{ route('upload.reset') }}" method="POST" id="formHapus" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- TABEL PREVIEW --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden min-h-[400px]">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h6 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Tampilan Data Transaksi (100 Teratas)
            </h6>
        </div>

        @if($dataExists)
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table id="sortableTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors group" onclick="sortTable(0)">
                                <div class="flex items-center gap-1">
                                    Tanggal Sewa
                                    <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors group" onclick="sortTable(1)">
                                <div class="flex items-center gap-1">
                                    Nama Penyewa
                                    <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors group" onclick="sortTable(2)">
                                <div class="flex items-center gap-1">
                                    Unit
                                    <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors group" onclick="sortTable(3)">
                                <div class="flex items-center gap-1">
                                    Layanan
                                    <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors group" onclick="sortTable(4)">
                                <div class="flex items-center justify-center gap-1">
                                    Jumlah Sewa
                                    <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewData as $dt)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 odd:bg-white even:bg-gray-50 odd:dark:bg-gray-800 even:dark:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($dt->tanggal_sewa)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $dt->nama_penyewa }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $dt->jenis_armada }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($dt->layanan == 'Lepas Kunci')
                                        <span
                                            class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 19.336 9.536 19.336 9.536 17.336 10.536 16.336 6.5 12.5 11 8">
                                                </path>
                                            </svg>
                                            Lepas Kunci
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center bg-cyan-100 text-cyan-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-cyan-900 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Dengan Driver
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ $dt->jumlah_sewa }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div
                class="p-6 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 italic">
                    *Hanya menampilkan 5 data terbaru dari basis data.
                </div>
                <div class="w-full md:w-auto">
                    {{ $previewData->links('pagination::tailwind') }}
                </div>
            </div>

        @else
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l4 4a1 1 0 01.586 1.414V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h5 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Data Belum Tersedia</h5>
                <p class="text-gray-500 dark:text-gray-400 max-w-sm">Siapkan file spreadsheet (.xlsx / .csv) Anda dan unggah
                    melalui panel di atas untuk mulai melihat data.</p>
            </div>
        @endif
    </div>

    {{-- MODAL LAYOUT: Tailwind --}}
    <div id="modalBackdrop" class="fixed inset-0 bg-black/50 z-[9998] hidden backdrop-blur-sm transition-opacity"
        onclick="tutupSemuaModal()"></div>

    {{-- MODAL 1: KONFIRMASI HAPUS --}}
    <div id="modalKonfirmasiHapus" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full transform transition-all scale-100">
            <div class="p-6 text-center">
                <div
                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/30 mb-6">
                    <svg class="h-8 w-8 text-red-600 dark:text-red-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Semua Data?</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Data transaksi, hasil analisis, dan riwayat yang
                    tersimpan akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex gap-3 justify-center">
                    <button onclick="tutupSemuaModal()"
                        class="px-5 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                        Batal
                    </button>
                    <button onclick="document.getElementById('formHapus').submit()"
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold shadow-lg shadow-red-500/30 transition">
                        Ya, Hapus Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 2: ERROR EKSTENSI --}}
    <div id="modalErrorEkstensi" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full relative">
            <button onclick="tutupSemuaModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="p-8 text-center">
                <div
                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/30 mb-6">
                    <svg class="h-8 w-8 text-red-600 dark:text-red-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Format File Tidak Sesuai</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Sistem hanya menerima file dokumen spreadsheet dengan
                    ekstensi:</p>
                <div class="flex justify-center gap-2 mb-6">
                    <span
                        class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm font-mono font-semibold text-gray-700 dark:text-gray-300">.xlsx</span>
                    <span
                        class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm font-mono font-semibold text-gray-700 dark:text-gray-300">.xls</span>
                    <span
                        class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm font-mono font-semibold text-gray-700 dark:text-gray-300">.csv</span>
                </div>
                <button onclick="tutupSemuaModal()"
                    class="w-full px-5 py-2.5 bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-lg font-semibold transition">
                    Mengerti
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL 3: ERROR TEMPLATE --}}
    <div id="modalErrorTemplate" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full relative">
            <button onclick="tutupSemuaModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="p-8 text-center">
                <div
                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 dark:bg-orange-900/30 mb-6">
                    <svg class="h-8 w-8 text-orange-600 dark:text-orange-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l4 4a1 1 0 01.586 1.414V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Struktur File Salah</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">File yang diunggah tidak sesuai dengan template
                    <strong>Dataset Transaksi Sembodo</strong>. Pastikan nama kolom dan urutan data sesuai.
                </p>
                <button onclick="tutupSemuaModal()"
                    class="w-full px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold transition">
                    Coba Lagi
                </button>
            </div>
        </div>
    </div>

    <script>
        // HELPER: Show/Hide Modals with Backdrop
        function bukaModal(id) {
            document.getElementById('modalBackdrop').classList.remove('hidden');
            document.getElementById(id).classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scroll
        }

        function tutupSemuaModal() {
            document.getElementById('modalBackdrop').classList.add('hidden');
            document.querySelectorAll('[id^="modal"]').forEach(el => {
                if (el.id !== 'modalBackdrop') el.classList.add('hidden');
            });
            document.body.style.overflow = 'auto'; // Restore scroll
        }

        // VALIDASI FILE
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

        // AUTO SHOW MODAL ON SESSION ERROR
        document.addEventListener("DOMContentLoaded", function () {
            @if(session('error_template'))
                bukaModal('modalErrorTemplate');
            @endif

            @if(session('error'))
                // Bisa buat modal khusus error umum atau pakai template alert
                // Disini contohnya pakai alert biasa yang sudah ada di atas,
                // tapi kalau mau modal:
                // bukaModal('modalErrorTemplate'); // atau buat modalErrorGeneral
            @endif
            });

        // SORTING TABLE FUNCTION
        // OPTIMIZED SORTING FUNCTION (Array-based)
        function sortTable(n) {
            const table = document.getElementById("sortableTable");
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.rows);
            
            // Determine order: default to 'asc', toggle if already 'asc'
            // We store the current order on the table header or table itself
            // For simplicity, let's store it on the specific header cell logic or just generic toggle
            // A simple way is to check the current sort state of the column
            
            // However, to keep it simple and consistent with previous logic ("dir" variable), 
            // we can check a custom attribute on the table.
            let dir = table.getAttribute("data-sort-dir") === "asc" ? "desc" : "asc";
            
            // Reset if clicking a different column (optional, but good UX)
            if (table.getAttribute("data-sort-col") !== String(n)) {
                dir = "asc";
            }
            
            table.setAttribute("data-sort-dir", dir);
            table.setAttribute("data-sort-col", n);

            // Sort the array of rows
            rows.sort(function(a, b) {
                let x = a.cells[n].innerText.toLowerCase();
                let y = b.cells[n].innerText.toLowerCase();

                // 1. DATE SORT (Index 0) - Format DD/MM/YYYY
                if (n === 0) {
                    const partsX = x.trim().split('/');
                    const partsY = y.trim().split('/');
                    const dateX = new Date(partsX[2], partsX[1] - 1, partsX[0]);
                    const dateY = new Date(partsY[2], partsY[1] - 1, partsY[0]);
                    return dir === "asc" ? dateX - dateY : dateY - dateX;
                }

                // 2. NUMBER SORT (Index 4) - Integer
                if (n === 4) {
                    const numX = parseInt(x) || 0;
                    const numY = parseInt(y) || 0;
                    return dir === "asc" ? numX - numY : numY - numX;
                }

                // 3. TEXT SORT (Default)
                // Use localeCompare for correct string sorting
                return dir === "asc" 
                    ? x.localeCompare(y, 'id', { numeric: true }) 
                    : y.localeCompare(x, 'id', { numeric: true });
            });

            // Re-append rows (moves them in the DOM)
            // Using a DocumentFragment is slightly faster but for < 500 rows direct append is fine
            // and modern browsers optimize this well.
            rows.forEach(row => tbody.appendChild(row));
            
            // Visual Feedback (Optional: update arrow icons if needed, 
            // but currently the icons are static in the HTML. 
            // We can leave them static or implementing a dynamic class toggle 
            // would require selecting specific TH elements).
            
            // For now, this optimization solely fixes the "heavy/laggy" issue.
        }
    </script>

@endsection