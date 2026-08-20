@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi Penjualan')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Transaksi Penjualan</h1>
        <div class="dt-breadcrumb">Penjualan / Transaksi</div>
    </div>
</div>

<div class="dt-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3">
            <input type="text" name="search" value="{{ request('search') }}" class="dt-input" placeholder="No. Transaksi / Kasir...">
        </div>
        <div class="col-sm-2">
            <select name="status" class="dt-select">
                <option value="">Semua Status</option>
                <option value="berhasil" {{ request('status')=='berhasil'?'selected':'' }}>Berhasil</option>
                <option value="dibatalkan" {{ request('status')=='dibatalkan'?'selected':'' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="col-sm-3">
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="dt-input">
        </div>
        <div class="col-sm-3">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="dt-input">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="dt-btn dt-btn-primary"><i class="bi bi-search"></i></button>
            @if(request()->hasAny(['search','status','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.transactions.index') }}" class="dt-btn dt-btn-outline"><i class="bi bi-x"></i></a>
            @endif
        </div>
    </form>
</div>

<div class="dt-card">
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>No Transaksi</th>
                    <th>Waktu</th>
                    <th>Kasir</th>
                    <th>Metode</th>
                    <th>Total Belanja</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $t->nomor_transaksi }}</span></td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->user->name }}</td>
                    <td><span class="dt-badge dt-badge-gold">{{ ucfirst($t->payment->metode ?? 'tunai') }}</span></td>
                    <td class="fw-600 text-navy">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td>
                        <span class="dt-badge {{ $t->status === 'berhasil' ? 'dt-badge-success' : 'dt-badge-danger' }}">
                            {{ ucfirst($t->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('admin.transactions.show', $t) }}">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                @if($t->status === 'berhasil')
                                    <div class="dt-menu-divider"></div>
                                    <form method="POST" action="{{ route('admin.transactions.cancel', $t) }}" onsubmit="return confirm('Batalkan transaksi {{ $t->nomor_transaksi }}?')">
                                        @csrf
                                        <button type="submit" class="dt-menu-danger">
                                            <i class="bi bi-x-circle"></i> Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $transactions->links() }}</div>
</div>
@endsection
