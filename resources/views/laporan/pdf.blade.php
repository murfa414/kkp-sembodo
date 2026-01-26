<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Armada - PT. Sembodo Rental Indonesia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }

        /* === HEADER === */
        .header {
            background-color: #1a5490;
            color: white;
            padding: 25px 30px;
            margin-bottom: 25px;
        }

        .header-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-size: 12px;
            opacity: 0.9;
        }

        .header-info {
            margin-top: 15px;
            font-size: 10px;
            opacity: 0.85;
        }

        .header-info span {
            margin-right: 20px;
        }

        /* === RINGKASAN STATISTIK === */
        .stats-container {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            padding: 0 20px;
        }

        .stat-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 15px 10px;
            border: 1px solid #eee;
            background: #f9f9f9;
        }

        .stat-box:first-child {
            border-radius: 8px 0 0 8px;
        }

        .stat-box:last-child {
            border-radius: 0 8px 8px 0;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #1a5490;
        }

        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* === SECTION HEADERS === */
        .section {
            margin: 0 20px 20px 20px;
            page-break-inside: avoid;
        }

        .section-header {
            padding: 12px 15px;
            border-radius: 8px 8px 0 0;
            color: white;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .section-header.laris {
            background-color: #4E73DF;
        }

        .section-header.sedang {
            background-color: #F6C23E;
            color: #333;
        }

        .section-header.kurang {
            background-color: #E74A3B;
        }

        .section-header-icon {
            margin-right: 10px;
            font-size: 16px;
        }

        .section-content {
            border: 1px solid #eee;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 20px;
            background: white;
        }

        /* === TABEL DATA === */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th {
            background-color: #f5f5f5;
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #ddd;
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }

        /* Striped rows (Bootstrap-like) */
        .data-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table .rank {
            font-weight: bold;
            color: #1a5490;
            width: 40px;
            text-align: center;
        }

        .data-table .vehicle-name {
            font-weight: bold;
            color: #333;
        }

        .data-table .stat-value {
            text-align: center;
            font-weight: bold;
        }

        .data-table .score {
            background-color: #4E73DF;
            color: white;
            padding: 3px 12px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        /* === INFO BOX === */
        .info-box {
            background: linear-gradient(135deg, #f8f9fc 0%, #eef2ff 100%);
            border-left: 4px solid #4E73DF;
            padding: 15px;
            margin-top: 15px;
            border-radius: 0 8px 8px 0;
        }

        .info-box-title {
            font-weight: bold;
            color: #1a5490;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .info-box-text {
            font-size: 10px;
            color: #555;
        }

        /* === FOOTER === */
        .footer {
            margin-top: 30px;
            padding: 15px 20px;
            background: #f5f5f5;
            border-top: 2px solid #ddd;
            font-size: 9px;
            color: #666;
            text-align: center;
        }

        .footer-logo {
            font-weight: bold;
            color: #1a5490;
            font-size: 11px;
        }

        /* === BADGE === */
        .badge {
            padding: 3px 12px;
            border-radius: 50px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-laris {
            background-color: #4E73DF;
            color: white;
        }

        .badge-sedang {
            background-color: #F6C23E;
            color: #333;
        }

        .badge-kurang {
            background-color: #E74A3B;
            color: white;
        }

        /* === SUMMARY ROW === */
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .summary-item {
            display: table-cell;
            padding: 10px;
        }

        .summary-count {
            font-size: 24px;
            font-weight: bold;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <div class="header">
        <div class="header-title">LAPORAN ANALISIS PERFORMA ARMADA</div>
        <div class="header-info">
            <span>Tanggal Cetak: {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span>
            <span>Perusahaan: PT. Sembodo Rent a Car</span>
        </div>
    </div>

    <!-- STATISTIK RINGKASAN -->
    <div class="stats-container">
        <div class="stat-box">
            <div class="stat-number" style="color: #4E73DF;">{{ $laris['count'] }}</div>
            <div class="stat-label">Armada Laris</div>
        </div>
        <div class="stat-box">
            <div class="stat-number" style="color: #F6C23E;">{{ $sedang['count'] }}</div>
            <div class="stat-label">Performa Sedang</div>
        </div>
        <div class="stat-box">
            <div class="stat-number" style="color: #E74A3B;">{{ $kurangLaris['count'] }}</div>
            <div class="stat-label">Kurang Diminati</div>
        </div>
    </div>

    <!-- SECTION 1: ARMADA LARIS -->
    <div class="section">
        <div class="section-header laris">
            <span class="section-header-icon">[PERFORMA TINGGI]</span>
            ARMADA PALING LARIS
        </div>
        <div class="section-content">
            <p style="margin-bottom: 15px; color: #666;">
                Armada dengan performa terbaik berdasarkan kombinasi frekuensi penyewaan dan total unit yang disewa.
                <strong>Rekomendasi:</strong> Pertahankan ketersediaan dan pertimbangkan untuk menambah unit.
            </p>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Nama Armada</th>
                        <th style="width: 100px; text-align: center;">Frekuensi</th>
                        <th style="width: 100px; text-align: center;">Total Unit</th>
                        <th style="width: 80px; text-align: center;">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laris['items'] as $index => $item)
                        <tr>
                            <td class="rank">#{{ $index + 1 }}</td>
                            <td class="vehicle-name">{{ $item['nama'] }}</td>
                            <td class="stat-value">{{ $item['c1'] ?? '-' }}</td>
                            <td class="stat-value">{{ $item['c2'] ?? '-' }}</td>
                            <td class="stat-value">
                                <span class="score">{{ ($item['c1'] ?? 0) + ($item['c2'] ?? 0) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999; font-style: italic;">Data tidak tersedia
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="info-box">
                <div class="info-box-title">💡 Insight Bisnis</div>
                <div class="info-box-text">
                    Armada kategori ini memiliki tingkat utilisasi tinggi. Pastikan unit selalu dalam kondisi prima dan
                    siap disewa.
                    Pertimbangkan untuk menawarkan paket premium atau perpanjang kontrak dengan pelanggan loyal.
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: PERFORMA SEDANG -->
    <div class="section">
        <div class="section-header sedang" style="margin-top: 20px;">
            <span class="section-header-icon">[PERFORMA SEDANG]</span>
            PERFORMA SEDANG
        </div>
        <div class="section-content">
            <p style="margin-bottom: 15px; color: #666;">
                Armada dengan tingkat penyewaan menengah. Masih memiliki potensi untuk ditingkatkan performanya.
            </p>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Nama Armada</th>
                        <th style="width: 100px; text-align: center;">Frekuensi</th>
                        <th style="width: 100px; text-align: center;">Total Unit</th>
                        <th style="width: 80px; text-align: center;">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sedang['items'] as $index => $item)
                        <tr>
                            <td class="rank">#{{ $index + 1 }}</td>
                            <td class="vehicle-name">{{ $item['nama'] }}</td>
                            <td class="stat-value">{{ $item['c1'] ?? '-' }}</td>
                            <td class="stat-value">{{ $item['c2'] ?? '-' }}</td>
                            <td class="stat-value">
                                <span class="score"
                                    style="background-color: #F6C23E; color: #333;">{{ ($item['c1'] ?? 0) + ($item['c2'] ?? 0) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999; font-style: italic;">Data tidak tersedia
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="info-box" style="border-left-color: #F6C23E;">
                <div class="info-box-title" style="color: #b8860b;">📈 Rekomendasi</div>
                <div class="info-box-text">
                    Evaluasi strategi pemasaran untuk armada ini. Pertimbangkan promo khusus atau bundling dengan
                    layanan tambahan.
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 3: KURANG DIMINATI -->
    <div class="section">
        <div class="section-header kurang" style="margin-top: 20px;">
            <span class="section-header-icon">[PERFORMA RENDAH]</span>
            KURANG DIMINATI
        </div>
        <div class="section-content">
            <p style="margin-bottom: 15px; color: #666;">
                Armada dengan tingkat penyewaan rendah. Memerlukan evaluasi dan strategi khusus untuk meningkatkan
                performa.
            </p>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Nama Armada</th>
                        <th style="width: 100px; text-align: center;">Frekuensi</th>
                        <th style="width: 100px; text-align: center;">Total Unit</th>
                        <th style="width: 80px; text-align: center;">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kurangLaris['items'] as $index => $item)
                        <tr>
                            <td class="rank">#{{ $index + 1 }}</td>
                            <td class="vehicle-name">{{ $item['nama'] }}</td>
                            <td class="stat-value">{{ $item['c1'] ?? '-' }}</td>
                            <td class="stat-value">{{ $item['c2'] ?? '-' }}</td>
                            <td class="stat-value">
                                <span class="score"
                                    style="background-color: #E74A3B;">{{ ($item['c1'] ?? 0) + ($item['c2'] ?? 0) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999; font-style: italic;">Data tidak tersedia
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="info-box" style="border-left-color: #E74A3B;">
                <div class="info-box-title" style="color: #c0392b;">🔍 Tindakan yang Disarankan</div>
                <div class="info-box-text">
                    • Evaluasi kondisi fisik kendaraan dan lakukan perbaikan jika diperlukan<br>
                    • Pertimbangkan penyesuaian harga sewa atau paket promo agresif<br>
                    • Analisis target pasar: apakah armada ini sesuai dengan kebutuhan pelanggan?<br>
                    • Jika performa tetap rendah, pertimbangkan untuk rotasi atau penggantian unit
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-logo">PT. Sembodo Rent a Car</div>
        <div style="margin-top: 5px;">
            <!-- Laporan ini dihasilkan secara otomatis oleh Sistem Analisis Data Mining K-Means Clustering<br> -->
            Dokumen ini bersifat rahasia dan hanya untuk keperluan internal perusahaan
        </div>
    </div>
</body>

</html>