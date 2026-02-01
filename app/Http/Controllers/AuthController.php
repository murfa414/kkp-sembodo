<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi; 


class AuthController extends Controller
{
    // 1. TAMPILKAN FORM LOGIN
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. PROSES LOGIN
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Coba login dengan data tersebut
        if (Auth::attempt($credentials)) {
            // Jika berhasil:
            $request->session()->regenerate(); // Buat session baru (keamanan)
            return redirect()->intended('/'); // Arahkan ke Beranda
        }

        // Jika gagal:
        return back()->withErrors([
            'username' => 'username atau kata sandi salah.',
        ])->onlyInput('username');
    }

    // 3. PROSES LOGOUT
    public function logout(Request $request)
    {   
        Transaksi::truncate(); 

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}