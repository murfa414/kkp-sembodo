@extends('layouts.admin')

@section('title', 'Hasil K-Means')
@section('page-title', 'Hasil Analisis Kategori')

@section('content')

<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-scatter me-2"></i> Grafik Persebaran Data</h6>
                {{-- <span class="badge bg-success">Proses Selesai pada perulangan ke-{{ $iterasi }}</span> --}}
            </div>
            <div class="card-body">
                <div id="kmeansChart" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary "><i class="fas fa-table me-2"></i> Detail Anggota Kategori</h6>
                {{-- <small class="text-muted fst-italic"><i></i> *Klik judul kolom untuk mengurutkan</small>  --}}
                {{-- class="fas fa-sort me-1" --}}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    {{-- Tambahkan ID resultTable disini --}}
                    <table class="table table-hover align-middle mb-0" id="resultTable">
                        <thead class="table-dark">
                            <tr>
                                {{-- Tambahkan onclick sortTable(...) dan ikon sort --}}
                                <th class="px-4" style="cursor: pointer;" onclick="sortTable(0)">
                                    Nama Unit<i class="fas fa-sort float-end mt-1 text-secondary"></i>
                                </th>
                                <th class="text-center" style="cursor: pointer;" onclick="sortTable(1)">
                                    Frekuensi Sewa <i class="fas fa-sort float-end mt-1 text-secondary"></i>
                                </th>
                                <th class="text-center" style="cursor: pointer;" onclick="sortTable(2)">
                                    Total Unit Keluar <i class="fas fa-sort float-end mt-1 text-secondary"></i>
                                </th>
                                
                                <th class="text-center text-white" style="cursor: pointer;" onclick="sortTable(3)">
                                    <i></i> Lepas Kunci <i class="fas fa-sort float-end mt-1 text-white-50"></i>
                                </th>
                                
                                <th class="text-center text-white" style="cursor: pointer;" onclick="sortTable(4)">
                                    <i></i> Dengan Driver <i class="fas fa-sort float-end mt-1 text-white-50"></i>
                                </th>
                                
                                {{-- <th class="text-center" style="cursor: pointer;" onclick="sortTable(5)">
                                    Klaster <i class="fas fa-sort float-end mt-1 text-secondary"></i>
                                </th> --}}
                                <th class="text-center" style="cursor: pointer;" onclick="sortTable(5)">
                                    Keterangan <i class="fas fa-sort float-end mt-1 text-secondary"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataArmada as $data)
                                <tr>
                                    <td class="px-4 fw-bold">{{ $data['nama'] }}</td>
                                    
                                    {{-- Tambahkan data-val untuk sorting angka yang akurat --}}
                                    <td class="text-center" data-val="{{ $data['c1'] }}">{{ $data['c1'] }} kali</td>
                                    <td class="text-center" data-val="{{ $data['c2'] }}">{{ $data['c2'] }} unit</td>
                                    
                                    <td class="text-center" data-val="{{ $data['lepas_kunci'] }}">
                                        @if($data['lepas_kunci'] > 0)
                                            {{ $data['lepas_kunci'] }}
                                        @else
                                            <span class="text-muted opacity-50">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center" data-val="{{ $data['driver'] }}">
                                        @if($data['driver'] > 0)
                                            {{ $data['driver'] }}
                                        @else
                                            <span class="text-muted opacity-50">-</span>
                                        @endif
                                    </td>
{{-- 
                                    <td class="text-center" data-val="{{ $data['klaster'] }}">
                                        @php
                                            $colors = ['bg-primary', 'bg-warning text-dark', 'bg-danger'];
                                            $bg = $colors[$data['klaster']] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $bg }} rounded-pill px-3">
                                            Klaster {{ $data['klaster'] + 1 }}
                                        </span>
                                    </td> --}}
                                    
                                    <td class="text-center" data-val="{{ $data['klaster'] }}">
                                        @if($data['klaster'] == 0) 
                                            {{-- Laris: Warna Biru --}}
                                            <span class="badge bg-primary rounded-pill px-3">Laris</span>
                                        @elseif($data['klaster'] == 1) 
                                            {{-- Sedang: Warna Kuning (Pakai text-dark biar tulisan kebaca) --}}
                                            <span class="badge bg-warning text-dark rounded-pill px-3">Sedang</span>
                                        @else 
                                            {{-- Kurang Laris: Warna Merah --}}
                                            <span class="badge bg-danger rounded-pill px-3">Kurang Laris</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- <div class="card-footer bg-white py-3">
                <a href="{{ route('kmeans.reset') }}" class="btn btn-warning border fw-bold">
                    <i class="fas fa-arrow-left me-2"></i> Analisis Ulang
                </a>
            </div> --}}
        </div>
    </div>
</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<script>
    var clusters = @json($clusters);
    var seriesData = [];
    var colors = ['#4E73DF', '#F6C23E', '#E74A3B', '#36B9CC', '#1CC88A'];
    var clusterNames = ['Laris', 'Sedang', 'Kurang Laris'];

    Object.keys(clusters).forEach(function(key, index) {
        var dataPoints = clusters[key].map(function(item) {
            return {
                x: parseInt(item.c1), 
                y: parseInt(item.c2), 
                name: item.nama       
            };
        });

        seriesData.push({
            name: clusterNames[index] || 'Klaster ' + (parseInt(index) + 1),
            color: colors[index],
            data: dataPoints
        });
    });

    Highcharts.chart('kmeansChart', {
        chart: { type: 'scatter', zoomType: 'xy' },
        title: { text: null },
        // --- TAMBAHKAN BAGIAN INI (EXPORTING) ---
        exporting: {
            buttons: {
                contextButton: {
                    //daftar ulang item menunya (TANPA 'printChart')
                    menuItems: [
                        "viewFullscreen",
                        "separator",
                        "downloadPNG",
                        "downloadJPEG",
                        "downloadSVG"
                    ]
                }
            }
        },
        // ----------------------------------------
        xAxis: { title: { text: 'Frekuensi Sewa (Kali)' }, startOnTick: true, endOnTick: true, showLastLabel: true },
        yAxis: { title: { text: 'Total Unit Keluar' } },
        legend: { layout: 'vertical', align: 'left', verticalAlign: 'top', x: 100, y: 70, floating: true, backgroundColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF', borderWidth: 1 },
        plotOptions: {
            scatter: {
                marker: { radius: 5, states: { hover: { enabled: true, lineColor: 'rgb(100,100,100)' } } },
                states: { hover: { marker: { enabled: false } } },
                tooltip: {
                    headerFormat: '<b>{series.name}</b><br>',
                    pointFormat: '{point.name}<br>Freq: {point.x}, Unit: {point.y}'
                }
            }
        },
        series: seriesData
    });

    // --- SCRIPT SORTING TABEL ---
    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("resultTable");
        switching = true;
        
        // Arah sorting pertama kali: ASC (Kecil ke Besar / A-Z)
        dir = "asc"; 
        
        while (switching) {
            switching = false;
            rows = table.rows;
            
            // Loop semua baris (kecuali header)
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                
                // Ambil 2 baris untuk dibandingkan
                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];
                
                // Cek apakah punya atribut 'data-val' (Untuk angka biar akurat)
                // Kalau tidak ada, pakai text biasa
                var xVal = x.getAttribute('data-val') ? parseFloat(x.getAttribute('data-val')) : x.innerText.toLowerCase();
                var yVal = y.getAttribute('data-val') ? parseFloat(y.getAttribute('data-val')) : y.innerText.toLowerCase();

                if (dir == "asc") {
                    if (xVal > yVal) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (xVal < yVal) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount ++;      
            } else {
                if (switchcount == 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    }
</script>

{{-- FITUR BARU: Floating Action Button (Tombol Melayang) --}}
<a href="{{ route('kmeans.reset') }}" 
   class="btn btn-warning rounded-circle shadow-lg position-fixed d-flex align-items-center justify-content-center"
   style="bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 1000;"
   data-bs-toggle="tooltip" title="Analisis Ulang">
    <i class="fas fa-sync-alt fa-lg text-dark"></i>
</a>

{{-- Script tambahan buat Tooltip FAB --}}
<script>
    document.addEventListener("DOMContentLoaded", function(){
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

@endsection