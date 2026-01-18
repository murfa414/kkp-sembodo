<div align="center">

# 🚗 Sembodo AI - Sistem Analisis Data Rental Mobil

![Laravel](https://img.shields.io/badge/Backend-Laravel_11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/Database-MySQL-00618A?style=for-the-badge&logo=mysql&logoColor=white)
![Algorithm](https://img.shields.io/badge/Analysis-K--Means_Clustering-blueviolet?style=for-the-badge)

**Transformasi Data Transaksi Menjadi Strategi Bisnis Cerdas**
<br>
Aplikasi web modern untuk menganalisis performa armada menggunakan algoritma Data Mining.

[Fitur Unggulan](#-fitur-unggulan) • [Instalasi](#-cara-instalasi) • [Panduan Penggunaan](#-panduan-penggunaan)

</div>

---

## ✨ Fitur Unggulan

Aplikasi ini dirancang untuk mempermudah manajemen dalam mengambil keputusan berbasis data:

*   📂 **Smart Import System**: Upload file Excel/CSV transaksi mentah, sistem otomatis merapikan data pelanggan dan kendaraan.
*   🧠 **Automated K-Means Analysis**: Algoritma cerdas yang mengelompokkan armada menjadi **Laris**, **Sedang**, dan **Kurang Diminati** secara otomatis.
*   📊 **Dashboard Eksekutif**: Visualisasi tren penyewaan bulanan dan proporsi layanan (Lepas Kunci vs Driver) dalam grafik interaktif.
*   📑 **Laporan Terstruktur**: Hasil analisis yang rapi dan siap digunakan untuk rapat manajemen.

---

## 🛠️ Persyaratan Sistem

Sebelum memulai, pastikan komputer Anda telah terpasang:
1.  **XAMPP / Laragon** (Pastikan PHP versi 8.2 ke atas).
2.  **Composer** (Untuk mengelola dependensi aplikasi).
3.  **Web Browser** (Google Chrome, Edge, atau Firefox).

---

## 🚀 Cara Instalasi (Langkah demi Langkah)

Ikuti panduan mudah ini untuk menjalankan aplikasi di komputer Anda:

### 1. Download & Ekstrak
Letakkan folder project ini di komputer Anda.

### 2. Siapkan Konfigurasi
Buka terminal (Command Prompt atau Git Bash) di dalam folder project, lalu jalankan perintah ini untuk menyalin konfigurasi:
```bash
copy .env.example .env
```
*Tips: Buka file `.env` dengan Notepad/VS Code dan pastikan nama database sesuai (misal: `DB_DATABASE=sembodo_db`).*

### 3. Install Dependensi
Download pustaka yang dibutuhkan aplikasi dengan perintah:
```bash
composer install
```

### 4. Setup Database Otomatis
Buat database kosong bernama `sembodo_db` (atau sesuai .env) di phpMyAdmin, lalu kembali ke terminal dan jalankan:
```bash
# Membuat key keamanan
php artisan key:generate

# Membuat tabel-tabel database
php artisan migrate

# Mengisi data master kategori kendaraan
php artisan db:seed --class=KategoriSeeder
```

### 5. Jalankan Aplikasi 🏁
Nyalakan server lokal dengan perintah:
```bash
php artisan serve
```
Sekarang buka browser dan kunjungi: **`http://localhost:8000`**

---

## 📖 Panduan Penggunaan

### 1️⃣ Login & Dashboard
Masuk menggunakan akun admin. Anda akan disambut Dashboard yang menampilkan ringkasan performa armada saat ini.

### 2️⃣ Import Data Transaksi
*   Masuk ke menu **Import / Upload**.
*   Upload file CSV.
*   Sistem akan otomatis memilah data:
    *   Mencatat **Pelanggan** baru.
    *   Mencatat **Kendaraan** dan plat nomornya.
    *   Mencatat detail **Transaksi** (Layanan, Wilayah, Durasi).

### 3️⃣ Jalankan Analisis
*   Masuk ke menu **Analisis K-Means**.
*   Tentukan jumlah klaster (Standard: 3 Klaster).
*   Pilih atribut analisis (Frekuensi Sewa & Total Unit).
*   Klik **"Proses Analisis"**.

### 4️⃣ Lihat Hasil & Laporan
Hasil pengelompokan akan muncul. Anda bisa melihat mobil mana yang menjadi "Primadona" (Laris) dan mana yang perlu dievaluasi. Data ini tersimpan dan bisa dilihat kapan saja di menu **Laporan**.

---

<div align="center">
    <p>Dikembangkan untuk <b>PT. Sembodo Rental Indonesia</b></p>
    <p>Dibuat oleh <a href="https://github.com/Lotsoo" target="_blank" rel="noopener noreferrer">lotso</a></p>
</div>
