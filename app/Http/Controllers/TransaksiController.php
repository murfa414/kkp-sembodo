<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Imports\TransaksiImport;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    // 1. Tampilkan Halaman Upload
    public function index()
    {
        $dataExists = Transaksi::exists();

        $previewData = [];
        $uploadedFiles = [];

        if ($dataExists) {
            $previewData = Transaksi::latest()->paginate(100);

            // Ambil daftar file unik beserta jumlah datanya
            $uploadedFiles = Transaksi::select('source_file', \DB::raw('count(*) as total'))
                ->groupBy('source_file')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('upload.index', compact('dataExists', 'previewData', 'uploadedFiles'));
    }

    // 2. Proses Import File
    public function import(Request $request)
    {
        // VALIDASI EKSTENSI
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ], [
            'file.mimes' => 'Format file tidak didukung! Harap unggah file dengan ekstensi .xlsx, .xls, atau .csv.'
        ]);

        try {
            // Ambil Info File
            $file = $request->file('file');
            $nama_file = $file->getClientOriginalName();
            $ukuran_byte = $file->getSize();
            $ukuran_kb = number_format($ukuran_byte / 1024, 2) . ' KB';

            // Cek mode import: replace (hapus semua) atau append (tambahkan)
            $replaceMode = $request->has('replace_data');

            if ($replaceMode) {
                // Mode Replace: Hapus semua data lama
                Transaksi::truncate();
            }

            // Import data baru (Kirim Nama File ke Constructor)
            Excel::import(new TransaksiImport($nama_file), $file);

            // Hitung total data setelah import
            $totalData = Transaksi::count();

            // SUKSES
            return redirect()->back()
                ->with('nama_file_aktual', $nama_file)
                ->with('ukuran_file_aktual', $ukuran_kb)
                ->with('total_data', $totalData)
                ->with('import_mode', $replaceMode ? 'replace' : 'append');

        } catch (\Exception $e) {

            // --- TANGKAP ERROR TEMPLATE ---
            if ($e->getMessage() == 'TEMPLATE_SALAH') {
                return redirect()->back()->with('error_template', true);
            }

            // Kalau error lain (Database mati, dll)
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // 3. Reset Data (Hapus Semua)
    public function destroy()
    {
        Transaksi::truncate();
        return redirect()->back()->with('success', 'Semua data berhasil dihapus');
    }

    // 4. Hapus Data Per File
    public function deleteByFile(Request $request)
    {
        $filename = $request->input('filename');

        if ($filename) {
            // Hapus data berdasarkan nama file
            $deleted = Transaksi::where('source_file', $filename)->delete();

            if ($deleted > 0) {
                return redirect()->back()->with('success', 'Data dari file "' . $filename . '" berhasil dihapus (' . $deleted . ' baris).');
            } else {
                return redirect()->back()->with('error', 'File tidak ditemukan atau data sudah kosong.');
            }
        }

        return redirect()->back()->with('error', 'Nama file tidak valid.');
    }
}