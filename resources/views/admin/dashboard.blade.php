@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

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

{{-- Stat Cards Row 1 --}}
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-xl-3">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-grid-3x3-gap"></i></div>
            <div class="dt-stat-label">Total Jenis Kain</div>
            <div class="dt-stat-value">{{ number_format($totalJenisKain) }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="dt-stat gold">
            <div class="dt-stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="dt-stat-label">Total Stok Rol</div>
            <div class="dt-stat-value">{{ number_format($totalStokRol) }} <small style="font-size:12px;font-weight:400">rol</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-rulers"></i></div>
            <div class="dt-stat-label">Total Stok Meter</div>
            <div class="dt-stat-value sm">{{ number_format($totalStokMeter, 1) }} <small style="font-size:12px;font-weight:400">m</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="dt-stat success">
            <div class="dt-stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="dt-stat-label">Pendapatan Hari Ini</div>
            <div class="dt-stat-value sm">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-md-4">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-arrow-down-circle"></i></div>
            <div class="dt-stat-label">Barang Masuk Hari Ini</div>
            <div class="dt-stat-value">{{ $barangMasukHariIni }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="dt-stat gold">
            <div class="dt-stat-icon"><i class="bi bi-receipt"></i></div>
            <div class="dt-stat-label">Transaksi Hari Ini</div>
            <div class="dt-stat-value">{{ $transaksiHariIni }}</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="dt-stat warning">
            <div class="dt-stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="dt-stat-label">Stok Menipis / Habis</div>
            <div class="dt-stat-value">{{ $jumlahStokMenipis }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Grafik Penjualan & Filter Keuangan --}}
    <div class="col-lg-7">
        <div class="dt-card h-100 d-flex flex-column">
            <div class="dt-card-header flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
                <div>
                    <span class="dt-card-title"><i class="bi bi-bar-chart me-2"></i>Penjualan & Omset</span>
                </div>

                {{-- Period Filter Buttons --}}
                <div class="d-flex flex-wrap gap-1" id="periodContainer">
                    <button type="button" class="chart-period-btn" onclick="filterChart('hari_ini', this)">Hari Ini</button>
                    <button type="button" class="chart-period-btn active" onclick="filterChart('minggu_ini', this)">7 Hari</button>
                    <button type="button" class="chart-period-btn" onclick="filterChart('bulan_ini', this)">Bulan Ini</button>
                    <button type="button" class="chart-period-btn" onclick="filterChart('tahun_ini', this)">Tahun Ini</button>
                </div>
            </div>

            {{-- Summary Indicators --}}
            <div class="row g-2 my-2 p-2 bg-light rounded text-center">
                <div class="col-6 col-sm-3 border-end">
                    <div class="text-muted" style="font-size:10px; font-weight:600; text-transform:uppercase;">Total Omset</div>
                    <div class="fw-700 text-navy" id="summaryOmset" style="font-size:13px;">-</div>
                </div>
                <div class="col-6 col-sm-3 border-end-0 border-sm-end">
                    <div class="text-muted" style="font-size:10px; font-weight:600; text-transform:uppercase;">Transaksi</div>
                    <div class="fw-700 text-navy" id="summaryTransaksi" style="font-size:13px;">-</div>
                </div>
                <div class="col-6 col-sm-3 border-end">
                    <div class="text-muted" style="font-size:10px; font-weight:600; text-transform:uppercase;">Terjual (Rol)</div>
                    <div class="fw-700 text-navy" id="summaryRol" style="font-size:13px;">-</div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="text-muted" style="font-size:10px; font-weight:600; text-transform:uppercase;">Terjual (Meter)</div>
                    <div class="fw-700 text-navy" id="summaryMeter" style="font-size:13px;">-</div>
                </div>
            </div>

            <div class="position-relative mt-2 flex-grow-1" style="min-height: 230px; max-height: 260px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Widget Real-Time Stok Menipis / Habis --}}
    <div class="col-lg-5">
        <div class="dt-card h-100 d-flex flex-column">
            <div class="dt-card-header">
                <div>
                    <span class="dt-card-title"><i class="bi bi-box me-2"></i>Stok Menipis / Habis</span>
                </div>
                <a href="{{ route('admin.stocks.index', ['status' => 'menipis']) }}" class="dt-btn dt-btn-outline dt-btn-xs">Lihat Semua</a>
            </div>
            @if($stokMenipis->isEmpty())
                <div class="empty-state py-4"><i class="bi bi-check-circle text-success fs-3"></i><p class="mt-2 mb-0">Semua stok kain aman</p></div>
            @else
            <div class="dt-table-wrap flex-grow-1">
                <table class="dt-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kain</th>
                            <th class="text-end">Stok Rol</th>
                            <th class="text-end">Total Meter</th>
                            <th class="text-center" style="white-space: nowrap;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($stokMenipis as $s)
                        <tr>
                            <td>
                                <div class="fw-600 text-navy" style="font-size:12.5px">{{ $s->fabric->nama_kain }}</div>
                                <div style="font-size:11px;color:var(--dt-muted)">{{ $s->fabric->kode_kain }}</div>
                            </td>
                            <td class="fw-700 text-navy text-end">{{ $s->stok_rol }} rol</td>
                            <td class="fw-600 text-navy text-end">
                                {{ number_format($s->total_meter, 1) }} m
                                @if($s->stok_meter > 0)
                                    <div style="font-size:10.5px;color:var(--dt-muted);font-weight:normal">(eceran {{ number_format($s->stok_meter, 1) }}m)</div>
                                @endif
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
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
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Kain Terlaris & Grafik Batang --}}
<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="dt-card h-100 d-flex flex-column">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-bar-chart-line me-2"></i>Grafik Batang Kain Terlaris</span>
            </div>
            <div class="p-3 position-relative flex-grow-1" style="min-height: 240px; max-height: 280px;">
                <canvas id="kainBarChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="dt-card h-100 d-flex flex-column">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-basket me-2"></i>Peringkat Kain Terlaris</span>
            </div>
            @if($kainTerlaris->isEmpty())
                <div class="empty-state py-4">
                    <i class="bi bi-inbox fs-3"></i>
                    <p class="mt-2 mb-0">Belum ada data penjualan kain.</p>
                </div>
            @else
            <div class="dt-table-wrap flex-grow-1">
                <table class="dt-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode & Nama Kain</th>
                            <th>Kategori</th>
                            <th>Volume Terjual</th>
                            <th class="text-end">Total Omset</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($kainTerlaris as $index => $item)
                        <tr>
                            <td style="color:var(--dt-muted); font-size:12px;">#{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-600 text-navy" style="font-size:13px">{{ $item->fabric->nama_kain ?? 'Kain Dihapus' }}</div>
                                <div style="font-size:11px;color:var(--dt-muted)">{{ $item->fabric->kode_kain ?? '-' }}</div>
                            </td>
                            <td>{{ $item->fabric->category->nama_kategori ?? '-' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($item->total_rol > 0)
                                        <span style="font-size:12px; font-weight:600; color:var(--dt-navy)">{{ number_format($item->total_rol) }} rol</span>
                                    @endif
                                    @if($item->total_meter > 0)
                                        <span style="font-size:12px; color:var(--dt-muted)">{{ number_format($item->total_meter, 1) }} m</span>
                                    @endif
                                </div>
                            </td>
                            <td class="fw-700 text-navy text-end" style="font-size:13.5px">Rp {{ number_format($item->total_omset, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Transaksi Terbaru --}}
    <div class="col-lg-7">
        <div class="dt-card h-100">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-receipt me-2"></i>Transaksi Terbaru</span>
                <a href="{{ route('admin.transactions.index') }}" class="dt-btn dt-btn-outline dt-btn-xs">Lihat Semua</a>
            </div>
            @if($transaksiTerbaru->isEmpty())
                <div class="empty-state py-4"><i class="bi bi-inbox fs-3"></i><p class="mt-2 mb-0">Belum ada transaksi</p></div>
            @else
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead><tr><th>No. Transaksi</th><th>Kasir</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($transaksiTerbaru as $t)
                        <tr>
                            <td>
                                <a href="{{ route('admin.transactions.show', $t) }}" class="fw-600 text-navy" style="text-decoration:none;font-size:13px">{{ $t->nomor_transaksi }}</a>
                                <div style="font-size:11px;color:var(--dt-muted)">{{ $t->created_at->diffForHumans() }}</div>
                            </td>
                            <td style="font-size:13px">{{ $t->user->name }}</td>
                            <td class="fw-600" style="font-size:13px">Rp {{ number_format($t->total,0,',','.') }}</td>
                            <td>
                                @if($t->status === 'berhasil')
                                    <span class="dt-badge dt-badge-success">Berhasil</span>
                                @else
                                    <span class="dt-badge dt-badge-danger">Dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Aktivitas Sistem --}}
    <div class="col-lg-5">
        <div class="dt-card h-100">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-journal-text me-2"></i>Aktivitas Terbaru</span>
                <a href="{{ route('admin.audit-logs.index') }}" class="dt-btn dt-btn-outline dt-btn-xs">Lihat Semua</a>
            </div>
            @forelse($aktivitasTerbaru as $log)
            <div style="padding:10px 0;border-bottom:1px solid var(--dt-border);font-size:13px">
                <div class="d-flex gap-2 align-items-start">
                    <div style="width:28px;height:28px;background:var(--dt-bg);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:700;color:var(--dt-navy)">
                        {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-600" style="color:var(--dt-navy)">{{ $log->user->name ?? 'Sistem' }}</div>
                        <div style="color:var(--dt-text)">{{ $log->aktivitas }}</div>
                        <div style="font-size:11px;color:var(--dt-muted)">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state py-4"><i class="bi bi-inbox fs-3"></i><p class="mt-2 mb-0">Belum ada aktivitas</p></div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let lineChartInstance = null;
let barChartInstance  = null;

function formatCurrencyIDR(val) {
    if (val >= 1000000000) {
        let n = val / 1000000000;
        return 'Rp ' + (n % 1 === 0 ? n : n.toFixed(1)) + ' M';
    }
    if (val >= 1000000) {
        let n = val / 1000000;
        return 'Rp ' + (n % 1 === 0 ? n : n.toFixed(1)) + ' Jt';
    }
    if (val >= 1000) {
        let n = val / 1000;
        return 'Rp ' + (n % 1 === 0 ? n : n.toFixed(1)) + ' Rb';
    }
    return 'Rp ' + val;
}

function initAdminCharts(labels, salesTotals, incomingTotals, barLabels, barData) {
    // 1. Grafik Garis Penjualan & Omset (Sleek Modern Executive Design)
    const ctxLine = document.getElementById('salesChart').getContext('2d');
    if (lineChartInstance) lineChartInstance.destroy();

    const gradientSales = ctxLine.createLinearGradient(0, 0, 0, 260);
    gradientSales.addColorStop(0, 'rgba(37, 99, 235, 0.20)');
    gradientSales.addColorStop(1, 'rgba(37, 99, 235, 0.00)');

    lineChartInstance = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Omset Penjualan (Rp)',
                    data: salesTotals,
                    borderColor: '#2563eb', // Royal Blue
                    backgroundColor: gradientSales,
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointStyle: 'circle',
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#2563eb',
                    pointHoverBackgroundColor: '#2563eb',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                },
                {
                    label: 'Nilai Pasokan Masuk (Rp)',
                    data: incomingTotals,
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
                    titleFont: { size: 12, weight: '600' },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => formatCurrencyIDR(v),
                        font: { size: 11 },
                        color: '#64748b',
                        padding: 6
                    },
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

    // 2. Grafik Batang Kain Terlaris (Slim Elegant Navy Bars)
    const ctxBarEl = document.getElementById('kainBarChart');
    if (ctxBarEl) {
        const ctxBar = ctxBarEl.getContext('2d');
        if (barChartInstance) barChartInstance.destroy();

        // Truncate labels so they don't tilt or clash
        const shortLabels = barLabels.map(l => (l && l.length > 15) ? l.substr(0, 13) + '...' : (l || '-'));

        barChartInstance = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: shortLabels,
                datasets: [{
                    label: 'Total Omset',
                    data: barData,
                    backgroundColor: ['#0f172a', '#1e3a8a', '#2563eb', '#0284c7', '#0d9488'],
                    hoverBackgroundColor: '#2563eb',
                    borderRadius: 6,
                    maxBarThickness: 28,
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
                            label: ctx => ' Total Omset: Rp ' + ctx.raw.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => formatCurrencyIDR(v),
                            font: { size: 10.5 },
                            color: '#64748b'
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10.5 },
                            color: '#475569',
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    }
}

function filterChart(period, btn) {
    if (btn) {
        document.querySelectorAll('.chart-period-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    fetch("{{ route('admin.dashboard.chartData') }}?period=" + period)
        .then(res => res.json())
        .then(data => {
            initAdminCharts(data.labels, data.totals, data.totals_incoming, data.bar_labels, data.bar_data);
            if (data.summary) {
                ['summaryOmset', 'summaryTransaksi', 'summaryRol', 'summaryMeter'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) delete el.dataset.animated;
                });
                document.getElementById('summaryOmset').innerText = data.summary.total_penjualan;
                document.getElementById('summaryTransaksi').innerText = data.summary.total_transaksi;
                document.getElementById('summaryRol').innerText = data.summary.total_rol;
                document.getElementById('summaryMeter').innerText = data.summary.total_meter;

                if (typeof window.initCounterAnimations === 'function') {
                    window.initCounterAnimations();
                }
            }
        })
        .catch(err => console.error('Gagal mengambil data grafik:', err));
}

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    const activeBtn = document.querySelector('.chart-period-btn.active');
    filterChart('minggu_ini', activeBtn);
});
</script>
@endpush
