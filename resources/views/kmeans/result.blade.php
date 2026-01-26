@extends('layouts.admin')

@section('title', 'Hasil K-Means')
@section('page-title', 'Hasil Analisis Kategori')

@section('content')

<div class="row">
    {{-- SCATTER CHART --}}
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-dark"><i class="fas fa-chart-scatter me-2"></i> Grafik Sebar Data</h6>
            </div>
            <div class="card-body">
                <div id="kmeansChart" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    {{-- RESULT TABLE --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-dark">Detail Anggota Kategori</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0" id="resultTable">
                        <thead class="table-dark sticky-top" style="z-index: 10;">
                            <tr>
                                <th class="px-4" style="cursor: pointer;" onclick="sortTable(0)">
                                    Nama Unit <i class="fas fa-sort float-end mt-1 text-secondary"></i>
                                </th>
                                <th class="text-center" style="cursor: pointer;" onclick="sortTable(1)">
                                    Frekuensi <i class="fas fa-sort float-end mt-1 text-secondary"></i>
                                </th>
                                <th class="text-center d-none d-md-table-cell" style="cursor: pointer;" onclick="sortTable(2)">
                                    Total Unit Keluar <i class="fas fa-sort float-end mt-1 text-secondary"></i>
                                </th>
                                <th class="text-center d-none d-lg-table-cell" style="cursor: pointer;" onclick="sortTable(3)">
                                    Lepas Kunci <i class="fas fa-sort float-end mt-1 text-white-50"></i>
                                </th>
                                <th class="text-center d-none d-lg-table-cell" style="cursor: pointer;" onclick="sortTable(4)">
                                    Dengan Driver <i class="fas fa-sort float-end mt-1 text-white-50"></i>
                                </th>
                                <th class="text-center" style="cursor: pointer;" onclick="sortTable(5)">
                                    Keterangan <i class="fas fa-sort float-end mt-1 text-secondary"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataArmada as $data)
                                <tr>
                                    <td class="px-4 fw-bold text-justify" style="text-align: justify; min-width: 200px;">{{ ucwords(strtolower($data['nama'])) }}</td>
                                    <td class="text-center" data-val="{{ $data['c1'] }}">{{ $data['c1'] }}</td>
                                    <td class="text-center d-none d-md-table-cell" data-val="{{ $data['c2'] }}">{{ $data['c2'] }}</td>
                                    <td class="text-center d-none d-lg-table-cell" data-val="{{ $data['lepas_kunci'] }}">
                                        @if($data['lepas_kunci'] > 0)
                                            {{ $data['lepas_kunci'] }}
                                        @else
                                            <span class="text-muted opacity-50">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center d-none d-lg-table-cell" data-val="{{ $data['driver'] }}">
                                        @if($data['driver'] > 0)
                                            {{ $data['driver'] }}
                                        @else
                                            <span class="text-muted opacity-50">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center" data-val="{{ $data['klaster'] }}">
                                        @if($data['klaster'] == 0) 
                                            <span class="badge bg-primary rounded-pill px-3" style="min-width: 100px;">Laris</span>
                                        @elseif($data['klaster'] == 1) 
                                            <span class="badge bg-warning text-dark rounded-pill px-3" style="min-width: 100px;">Sedang</span>
                                        @else 
                                            <span class="badge bg-danger rounded-pill px-3" style="min-width: 100px;">Kurang Laris</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Floating Action Button --}}
<a href="{{ route('kmeans.reset') }}" 
   class="fab btn btn-warning shadow-lg"
   data-bs-toggle="tooltip" title="Analisis Ulang">
    <i class="fas fa-sync-alt fa-lg text-dark"></i>
</a>

@endsection

@push('scripts')
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
        exporting: {
            buttons: {
                contextButton: {
                    menuItems: ["viewFullscreen", "separator", "downloadPNG", "downloadJPEG", "downloadSVG"]
                }
            }
        },
        xAxis: { title: { text: 'Frekuensi Sewa (Kali)' }, startOnTick: true, endOnTick: true, showLastLabel: true },
        yAxis: { title: { text: 'Total Unit Keluar Keluar' } },
        legend: { layout: 'vertical', align: 'left', verticalAlign: 'top', x: 100, y: 70, floating: true, backgroundColor: '#FFFFFF', borderWidth: 1 },
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

    // Table Sorting
    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("resultTable");
        switching = true;
        dir = "asc"; 
        
        while (switching) {
            switching = false;
            rows = table.rows;
            
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];
                
                var xVal = x.getAttribute('data-val') ? parseFloat(x.getAttribute('data-val')) : x.innerText.toLowerCase();
                var yVal = y.getAttribute('data-val') ? parseFloat(y.getAttribute('data-val')) : y.innerText.toLowerCase();

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

    // Tooltip Init
    document.addEventListener("DOMContentLoaded", function(){
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el) });
    });
</script>
@endpush