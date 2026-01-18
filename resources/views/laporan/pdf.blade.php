<!DOCTYPE html>
<html>
<head>
    <title>Laporan KMeans</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2 { color: #4E73DF; margin-bottom: 0.5rem; }
        .section { margin-bottom: 30px; }
        ul { padding-left: 20px; }
        .kurang-laris { color: #E74A3B; }
        .sedang { color: #F6C23E; }
        .laris { color: #4E73DF; }
        hr { border: 0; border-top: 1px solid #ccc; margin: 1rem 0; }
    </style>
</head>
<body>
    <h1>Laporan Hasil Analisis KMeans</h1>
    <p>Tanggal: {{ date('d/m/Y H:i') }}</p>

    <div class="section">
        <h2 class="kurang-laris">Kurang Laris (Merah)</h2>
        <p><strong>Total: </strong>{{ $kurangLaris['count'] }}</p>
        <p>3 Teratas:</p>
        <ul>
            @forelse($kurangLaris['top3'] as $index => $item)
                <li>{{ $index + 1 }}. {{ $item['nama'] }}</li>
            @empty
                <li>- Data Kosong -</li>
            @endforelse
        </ul>
    </div>

    <hr>

    <div class="section">
        <h2 class="sedang">Sedang (Kuning)</h2>
        <p><strong>Total: </strong>{{ $sedang['count'] }}</p>
        <p>3 Teratas:</p>
        <ul>
            @forelse($sedang['top3'] as $index => $item)
                <li>{{ $index + 1 }}. {{ $item['nama'] }}</li>
            @empty
                <li>- Data Kosong -</li>
            @endforelse
        </ul>
    </div>

    <hr>

    <div class="section">
        <h2 class="laris">Laris (Biru)</h2>
        <p><strong>Total: </strong>{{ $laris['count'] }}</p>
        <p>3 Teratas:</p>
        <ul>
            @forelse($laris['top3'] as $index => $item)
                <li>{{ $index + 1 }}. {{ $item['nama'] }}</li>
            @empty
                <li>- Data Kosong -</li>
            @endforelse
        </ul>
    </div>
</body>
</html>
