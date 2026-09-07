@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi Penjualan')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Transaksi Penjualan</h1>
        <div class="dt-breadcrumb">Penjualan / Transaksi</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <!-- Unified Filter Toolbar -->
    <div class="p-3 bg-white border-bottom">
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group min-w-0">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0 bg-light" placeholder="No. Transaksi / Kasir..." style="font-size: 13.5px;" onchange="this.form.submit()">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select bg-light" style="font-size: 13.5px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="berhasil" {{ request('status')=='berhasil'?'selected':'' }}>Berhasil</option>
                    <option value="dibatalkan" {{ request('status')=='dibatalkan'?'selected':'' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="form-control bg-light" style="font-size: 13.5px;" onchange="this.form.submit()" title="Dari Tanggal">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="form-control bg-light" style="font-size: 13.5px;" onchange="this.form.submit()" title="Sampai Tanggal">
            </div>
            <div class="col-12 col-md-2 text-md-end ms-auto">
                @if(request()->hasAny(['search','status','tanggal_dari','tanggal_sampai']))
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-light border text-muted" title="Reset Filter">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="dt-table-wrap">
        <table class="dt-table mb-0 align-middle">
            <thead>
                <tr>
                    <th>No Transaksi</th>
                    <th>Waktu</th>
                    <th>Kasir</th>
                    <th>Metode</th>
                    <th class="text-end">Total Belanja</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $t->nomor_transaksi }}</span></td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->user->name }}</td>
                    <td><span class="dt-badge dt-badge-gold">{{ ucfirst($t->payment->metode ?? 'tunai') }}</span></td>
                    <td class="fw-600 text-end text-navy">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="dt-badge {{ $t->status === 'berhasil' ? 'dt-badge-success' : 'dt-badge-danger' }}">
                            {{ ucfirst($t->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('admin.transactions.show', $t) }}">
                                    <i class="bi bi-eye me-1.5"></i> Detail Transaksi
                                </a>
                                <button type="button" onclick="printReceiptDirect('{{ route('admin.sales.receipt', $t) }}')">
                                    <i class="bi bi-printer me-1.5"></i> Cetak Struk
                                </button>
                                @if($t->status === 'berhasil')
                                    <div class="dt-menu-divider"></div>
                                    <form method="POST" action="{{ route('admin.transactions.cancel', $t) }}" onsubmit="return confirm('Batalkan transaksi {{ $t->nomor_transaksi }}?')">
                                        @csrf
                                        <button type="submit" class="dt-menu-danger">
                                            <i class="bi bi-x-circle me-1.5"></i> Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada transaksi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top bg-light bg-opacity-30">{{ $transactions->links() }}</div>
</div>
@endsection
