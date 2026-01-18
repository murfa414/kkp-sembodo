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
        if($dataExists) {
            $previewData = Transaksi::latest()->paginate(100);
        }

        return view('upload.index', compact('dataExists', 'previewData'));
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

            // Bersihkan & Import
            Transaksi::truncate(); 
            Excel::import(new TransaksiImport, $file);

            // SUKSES
            return redirect()->back()
                // ->with('success', 'Data transaksi berhasil diimpor')
                ->with('nama_file_aktual', $nama_file)
                ->with('ukuran_file_aktual', $ukuran_kb);
    
        } catch (\Exception $e) {
            
            // --- TANGKAP ERROR TEMPLATE ---
            // Kalau errornya 'TEMPLATE_SALAH', kita kirim sinyal 'error_template'
            // Supaya di View nanti muncul MODAL ERROR, bukan cuma alert biasa.
            if ($e->getMessage() == 'TEMPLATE_SALAH') {
                return redirect()->back()->with('error_template', true); 
            }

            // Kalau error lain (Database mati, dll)
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
    
    // 3. Reset Data
    public function destroy() {
        Transaksi::truncate();
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}