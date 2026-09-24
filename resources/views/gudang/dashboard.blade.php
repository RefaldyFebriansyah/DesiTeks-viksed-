@extends('layouts.app')
@section('title', 'Dashboard Gudang')
@section('page-title', 'Dashboard Gudang')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.chart-period-btn {
    padding: 4px 12px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
}
.chart-period-btn:hover {
    background: #f8fafc;
    color: #0f172a;
}
.chart-period-btn.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
}
</style>
@endpush

@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-grid-3x3-gap fs-5"></i></div>
            <div class="dt-stat-label">Total Jenis Kain</div>
            <div class="dt-stat-value">{{ number_format($totalJenisKain) }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="dt-stat gold">
            <div class="dt-stat-icon"><i class="bi bi-box-seam fs-5"></i></div>
            <div class="dt-stat-label">Total Stok Rol</div>
            <div class="dt-stat-value">{{ number_format($totalStokRol) }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-rulers fs-5"></i></div>
            <div class="dt-stat-label">Total Stok Meter</div>
            <div class="dt-stat-value sm">{{ number_format($totalStokMeter, 1) }} m</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="dt-stat success">
            <div class="dt-stat-icon"><i class="bi bi-arrow-down-circle fs-5"></i></div>
            <div class="dt-stat-label">Barang Masuk Hari Ini</div>
            <div class="dt-stat-value">{{ $barangMasukHariIni }}</div>
        </div>
    </div>
</div>

{{-- Section Grafik Line & Grafik Batang --}}
<div class="row g-3 mb-4">
    {{-- Grafik Garis Tren Pasokan Barang Masuk --}}
    <div class="col-lg-7">
        <div class="dt-card h-100 d-flex flex-column">
            <div class="dt-card-header flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
                <div>
                    <span class="dt-card-title"><i class="bi bi-graph-up-arrow me-2"></i>Tren Pasokan Barang Masuk</span>
                </div>
                <div class="d-flex flex-wrap gap-1" id="gudangPeriodContainer">
                    <button type="button" class="chart-period-btn" onclick="filterGudangChart('hari_ini', this)">Hari Ini</button>
                    <button type="button" class="chart-period-btn active" onclick="filterGudangChart('minggu_ini', this)">7 Hari</button>
                    <button type="button" class="chart-period-btn" onclick="filterGudangChart('bulan_ini', this)">Bulan Ini</button>
                    <button type="button" class="chart-period-btn" onclick="filterGudangChart('tahun_ini', this)">Tahun Ini</button>
                </div>
            </div>
            <div class="p-3 position-relative flex-grow-1" style="min-height: 240px; max-height: 270px;">
                <canvas id="gudangLineChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Grafik Batang Stok Real per Jenis Kain (Maksimal 25 Rol per Kain) --}}
    <div class="col-lg-5">
        <div class="dt-card h-100 d-flex flex-column">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-bar-chart-line me-2"></i>Stok Real per Jenis Kain (Max 25 Rol)</span>
            </div>
            <div class="p-3 position-relative flex-grow-1" style="min-height: 240px; max-height: 270px;">
                <canvas id="gudangBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Table & Low Stock Section --}}
<div class="row g-3">
    <div class="col-lg-8">
        <div class="dt-card h-100 d-flex flex-column">
            <div class="dt-card-header flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
                <span class="dt-card-title"><i class="bi bi-arrow-down-square me-2"></i>Barang Masuk Terbaru</span>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <a href="{{ route('gudang.incoming-goods.index') }}" class="dt-btn dt-btn-outline dt-btn-xs">Lihat Semua</a>
                    <a href="{{ route('gudang.incoming-goods.create') }}" class="dt-btn dt-btn-primary dt-btn-sm">
                        <i class="bi bi-plus-lg"></i> Barang Masuk
                    </a>
                </div>
            </div>
            @if($barangMasukTerbaru->isEmpty())
                <div class="empty-state py-4"><i class="bi bi-inbox fs-3"></i><p class="mt-2 mb-0">Belum ada data barang masuk</p></div>
            @else
            <div class="dt-table-wrap flex-grow-1">
                <table class="dt-table" style="width: 100%; table-layout: fixed;">
                    <thead><tr><th style="width: 22%;">No. Faktur</th><th style="width: 26%;">Supplier</th><th style="width: 15%;">Tanggal</th><th style="width: 12%;">Rol</th><th style="width: 12%;">Meter</th><th class="text-end" style="width: 13%;">Total</th></tr></thead>
                    <tbody>
                    @foreach($barangMasukTerbaru as $bg)
                        <tr>
                            <td><a href="{{ route('gudang.incoming-goods.show', $bg) }}" class="fw-600 text-navy dt-truncate" style="text-decoration:none; max-width: 100%; display: block;" title="{{ $bg->nomor_faktur }}">{{ $bg->nomor_faktur }}</a></td>
                            <td><span class="dt-truncate" title="{{ $bg->supplier->nama_supplier }}" style="max-width: 100%;">{{ $bg->supplier->nama_supplier }}</span></td>
                            <td>{{ $bg->tanggal->format('d/m/Y') }}</td>
                            <td><span class="fw-600 text-navy">{{ $bg->total_rol }} rol</span></td>
                            <td>{{ number_format($bg->total_meter,1) }} m</td>
                            <td class="fw-600 text-navy text-end" style="font-size: 12.5px;">Rp {{ number_format($bg->total_pembelian,0,',','.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="dt-card h-100 d-flex flex-column">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-exclamation-triangle me-2" style="color:var(--dt-warning)"></i>Stok Menipis</span>
                <a href="{{ route('gudang.stocks.index') }}" class="dt-btn dt-btn-outline dt-btn-xs">Lihat Stok</a>
            </div>
            @if($stokMenipis->isEmpty())
                <div class="empty-state py-4"><i class="bi bi-check-circle text-success fs-3"></i><p class="mt-2 mb-0">Semua stok aman</p></div>
            @else
            <div class="px-3 py-2 flex-grow-1">
                @foreach($stokMenipis as $s)
                <div style="padding:10px 0;border-bottom:1px solid var(--dt-border);font-size:13px">
                    <div class="fw-600 text-navy">{{ $s->fabric->nama_kain }}</div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span style="color:var(--dt-muted);font-size:12px">{{ $s->fabric->kode_kain }} • {{ $s->stok_rol }} rol ({{ number_format($s->total_meter,1) }}m)</span>
                        @php $statusStok = $s->fabric?->status_stok ?? 'habis'; @endphp
                        @if($statusStok === 'habis')
                            <span class="badge-stok-habis"><i class="bi bi-x-circle-fill me-1"></i> Habis</span>
                        @elseif($statusStok === 'penuh')
                            <span class="badge-stok-over"><i class="bi bi-box-fill me-1"></i> Stok Penuh</span>
                        @elseif($statusStok === 'menipis')
                            <span class="badge-stok-menipis"><i class="bi bi-exclamation-triangle-fill me-1"></i> Stok Menipis</span>
                        @else
                            <span class="badge-stok-aman"><i class="bi bi-check-circle-fill me-1"></i> Stok Aman</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let gudangLineChart = null;
let gudangBarChart  = null;

function initGudangCharts(labels, rolData, fakturData, barLabels, barData) {
    // 1. Line Chart Gudang (Cyan & Amber Gradient)
    const ctxLine = document.getElementById('gudangLineChart').getContext('2d');
    if (gudangLineChart) gudangLineChart.destroy();

    const gradientRol = ctxLine.createLinearGradient(0, 0, 0, 260);
    gradientRol.addColorStop(0, 'rgba(6, 182, 212, 0.20)');
    gradientRol.addColorStop(1, 'rgba(6, 182, 212, 0.00)');

    gudangLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Volume Pasokan (Rol)',
                    data: rolData,
                    borderColor: '#06b6d4', // Cyan
                    backgroundColor: gradientRol,
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointStyle: 'circle',
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#06b6d4',
                    pointHoverBackgroundColor: '#06b6d4',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                },
                {
                    label: 'Jumlah Faktur Masuk',
                    data: fakturData,
                    borderColor: '#f59e0b', // Amber / Gold
                    backgroundColor: '#f59e0b',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.35,
                    fill: false,
                    pointStyle: 'circle',
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#f59e0b',
                    pointHoverBackgroundColor: '#f59e0b',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        boxHeight: 8,
                        padding: 16,
                        font: { size: 12, weight: '600' }
                    }
                },
                tooltip: {
                    usePointStyle: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    backgroundColor: '#0f172a',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { font: { size: 11 }, color: '#64748b', precision: 0 },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 11 },
                        color: '#64748b',
                        maxRotation: 0,
                        minRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 7
                    }
                }
            }
        }
    });

    // 2. Bar Chart Gudang (Stok Real per Jenis Kain - Max 25 Rol per Kain)
    const ctxBar = document.getElementById('gudangBarChart').getContext('2d');
    if (gudangBarChart) gudangBarChart.destroy();

    const shortBarLabels = barLabels.map(l => (l && l.length > 14) ? l.substr(0, 12) + '...' : (l || '-'));

    const barColors = barData.map(v => {
        if (v >= 25) return '#10b981'; // Green (Stok Penuh)
        if (v <= 5)  return '#f59e0b'; // Amber (Stok Menipis)
        return '#2563eb';              // Blue (Stok Aman)
    });

    gudangBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: shortBarLabels,
            datasets: [{
                label: 'Stok Rol',
                data: barData,
                backgroundColor: barColors,
                borderRadius: 6,
                maxBarThickness: 24,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        title: items => barLabels[items[0].dataIndex] || '',
                        label: ctx => {
                            const val = ctx.raw;
                            let statusText = 'Stok Aman';
                            if (val >= 25) statusText = 'Stok Penuh';
                            else if (val <= 5) statusText = 'Stok Menipis';
                            return ' Stok Real: ' + val + ' / 25 Rol (' + statusText + ')';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 25,
                    ticks: { font: { size: 10.5 }, color: '#64748b', stepSize: 5 },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 10 },
                        color: '#475569',
                        maxRotation: 45,
                        minRotation: 0,
                        autoSkip: false
                    }
                }
            }
        }
    });
}

function filterGudangChart(period, btn) {
    if (btn) {
        document.querySelectorAll('#gudangPeriodContainer .chart-period-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    fetch("{{ route('gudang.dashboard.chartData') }}?period=" + period)
        .then(res => res.json())
        .then(data => {
            initGudangCharts(data.labels, data.rol_masuk, data.faktur_masuk, data.bar_labels, data.bar_data);
        })
        .catch(err => console.error('Gagal mengambil data grafik gudang:', err));
}

document.addEventListener('DOMContentLoaded', () => {
    const activeBtn = document.querySelector('#gudangPeriodContainer .chart-period-btn.active');
    filterGudangChart('minggu_ini', activeBtn);
});
</script>
@endpush
