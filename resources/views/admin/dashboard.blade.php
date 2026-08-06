@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- Stat Cards Row 1 --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-grid-3x3-gap fs-5"></i></div>
            <div class="dt-stat-label">Total Jenis Kain</div>
            <div class="dt-stat-value">{{ number_format($totalJenisKain) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dt-stat gold">
            <div class="dt-stat-icon"><i class="bi bi-box-seam fs-5"></i></div>
            <div class="dt-stat-label">Total Stok Rol</div>
            <div class="dt-stat-value">{{ number_format($totalStokRol) }} <small style="font-size:14px;font-weight:400">rol</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-rulers fs-5"></i></div>
            <div class="dt-stat-label">Total Stok Meter</div>
            <div class="dt-stat-value sm">{{ number_format($totalStokMeter, 1) }} <small style="font-size:14px;font-weight:400">m</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="dt-stat success">
            <div class="dt-stat-icon"><i class="bi bi-cash-stack fs-5"></i></div>
            <div class="dt-stat-label">Pendapatan Hari Ini</div>
            <div class="dt-stat-value sm">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="dt-stat">
            <div class="dt-stat-icon"><i class="bi bi-arrow-down-circle fs-5"></i></div>
            <div class="dt-stat-label">Barang Masuk Hari Ini</div>
            <div class="dt-stat-value">{{ $barangMasukHariIni }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="dt-stat gold">
            <div class="dt-stat-icon"><i class="bi bi-receipt fs-5"></i></div>
            <div class="dt-stat-label">Transaksi Hari Ini</div>
            <div class="dt-stat-value">{{ $transaksiHariIni }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="dt-stat warning">
            <div class="dt-stat-icon"><i class="bi bi-exclamation-triangle fs-5"></i></div>
            <div class="dt-stat-label">Stok Menipis</div>
            <div class="dt-stat-value">{{ $stokMenipis->count() }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Grafik Penjualan --}}
    <div class="col-lg-7">
        <div class="dt-card h-100">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-bar-chart me-2 text-gold"></i>Penjualan 7 Hari Terakhir</span>
            </div>
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    {{-- Stok Menipis --}}
    <div class="col-lg-5">
        <div class="dt-card h-100">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-exclamation-triangle me-2" style="color:var(--dt-warning)"></i>Stok Menipis / Habis</span>
                <a href="{{ route('admin.stocks.index') }}" class="dt-btn dt-btn-outline dt-btn-xs">Lihat Semua</a>
            </div>
            @if($stokMenipis->isEmpty())
                <div class="empty-state py-4"><i class="bi bi-check-circle text-success fs-3"></i><p class="mt-2 mb-0">Semua stok aman</p></div>
            @else
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead><tr><th>Kain</th><th>Stok (m)</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($stokMenipis as $s)
                        <tr>
                            <td>
                                <div class="fw-600" style="font-size:13px">{{ $s->fabric->nama_kain }}</div>
                                <div style="font-size:11px;color:var(--dt-muted)">{{ $s->fabric->kode_kain }}</div>
                            </td>
                            <td>{{ number_format($s->stok_meter, 1) }}</td>
                            <td>
                                @if($s->stok_meter <= 0)
                                    <span class="dt-badge dt-badge-danger">Habis</span>
                                @else
                                    <span class="dt-badge dt-badge-warning">Menipis</span>
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

<div class="row g-3">
    {{-- Transaksi Terbaru --}}
    <div class="col-lg-7">
        <div class="dt-card h-100">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-receipt me-2 text-navy"></i>Transaksi Terbaru</span>
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
const ctx = document.getElementById('salesChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($grafikData, 'label')) !!},
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: {!! json_encode(array_column($grafikData, 'total')) !!},
            backgroundColor: 'rgba(15,39,68,.75)',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k',
                    font: { size: 11 }
                },
                grid: { color: 'rgba(0,0,0,.05)' }
            },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});
</script>
@endpush
