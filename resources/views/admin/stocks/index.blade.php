@extends('layouts.app')
@section('title', 'Manajemen Stok')
@section('page-title', 'Stok Kain & Penyesuaian')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Stok Kain</h1>
        <div class="dt-breadcrumb">Gudang / Stok</div>
    </div>
</div>

<div class="dt-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-5">
            <input type="text" name="search" value="{{ request('search') }}" class="dt-input" placeholder="Cari kode atau nama kain...">
        </div>
        <div class="col-sm-3">
            <select name="status" class="dt-select">
                <option value="">Semua Status Stok</option>
                <option value="tersedia" {{ request('status')=='tersedia'?'selected':'' }}>Tersedia</option>
                <option value="menipis" {{ request('status')=='menipis'?'selected':'' }}>Menipis</option>
                <option value="habis" {{ request('status')=='habis'?'selected':'' }}>Habis</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="dt-btn dt-btn-primary"><i class="bi bi-search"></i> Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.stocks.index') }}" class="dt-btn dt-btn-outline"><i class="bi bi-x"></i> Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="dt-card">
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kain</th>
                    <th>Kategori</th>
                    <th>Stok Rol</th>
                    <th>Stok Meter</th>
                    <th>Status</th>
                    <th>Terakhir Update</th>
                    <th>Penyesuaian</th>
                </tr>
            </thead>
            <tbody>
            @forelse($stocks as $stock)
                @php
                    $status = $stock->status;
                    $badgeClass = match($status) { 'habis'=>'dt-badge-danger','menipis'=>'dt-badge-warning', default=>'dt-badge-success' };
                @endphp
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $stock->fabric->kode_kain }}</span></td>
                    <td class="fw-600 text-navy">{{ $stock->fabric->nama_kain }}</td>
                    <td>{{ $stock->fabric->category->nama_kategori }}</td>
                    <td class="fw-600">{{ $stock->stok_rol }} rol</td>
                    <td class="fw-600">{{ number_format($stock->stok_meter, 1) }} m</td>
                    <td><span class="dt-badge {{ $badgeClass }}">{{ ucfirst($status) }}</span></td>
                    <td><small class="text-muted">{{ $stock->updated_at ? $stock->updated_at->diffForHumans() : '-' }}</small></td>
                    <td>
                        <button type="button" class="dt-btn dt-btn-gold dt-btn-xs" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $stock->fabric_id }}">
                            <i class="bi bi-sliders"></i> Adjust
                        </button>

                        {{-- Modal Adjust --}}
                        <div class="modal fade" id="adjustModal{{ $stock->fabric_id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content dt-card" style="padding:0; overflow:hidden;">
                                    <div class="dt-card-header bg-light mb-0" style="border-radius:0;">
                                        <h5 class="dt-card-title m-0">Penyesuaian Stok: {{ $stock->fabric->nama_kain }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.stocks.adjust', $stock->fabric_id) }}">
                                        @csrf
                                        <div class="p-3">
                                            <div class="mb-3">
                                                <label class="dt-label">Stok Rol Baru</label>
                                                <input type="number" name="stok_rol" class="dt-input" value="{{ $stock->stok_rol }}" required min="0">
                                            </div>
                                            <div class="mb-3">
                                                <label class="dt-label">Stok Meter Baru</label>
                                                <input type="number" step="0.01" name="stok_meter" class="dt-input" value="{{ $stock->stok_meter }}" required min="0">
                                            </div>
                                            <div class="mb-3">
                                                <label class="dt-label">Alasan Penyesuaian</label>
                                                <input type="text" name="keterangan" class="dt-input" placeholder="Misal: Stok opname / Barang rusak" required>
                                            </div>
                                        </div>
                                        <div class="p-3 bg-light text-end">
                                            <button type="button" class="dt-btn dt-btn-outline me-2" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="dt-btn dt-btn-primary">Simpan Stok</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data stok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $stocks->links() }}</div>
</div>
@endsection
