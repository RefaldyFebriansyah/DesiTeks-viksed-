@extends('layouts.app')
@section('title', 'Data Kain')
@section('page-title', 'Data Kain')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Data Kain</h1>
        <div class="dt-breadcrumb">Master Data / Kain</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <!-- Unified Toolbar Card: Search, Filters & Action Button -->
    <div class="p-3 bg-white border-bottom">
        <form method="GET" action="{{ route('admin.fabrics.index') }}" id="searchForm" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group min-w-0">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="searchInput" autocomplete="off" class="form-control border-start-0 ps-0 bg-light" placeholder="Cari kode, nama, warna..." value="{{ request('search') }}" style="font-size: 13.5px;">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="category_id" class="form-select bg-light" style="font-size: 13.5px;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id ? 'selected':'' }}>{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select bg-light" style="font-size: 13.5px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status')=='nonaktif'?'selected':'' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-12 col-md-2 text-md-end ms-auto d-flex align-items-center justify-content-end gap-2">
                @if(request()->hasAny(['search','category_id','status']))
                    <a href="{{ route('admin.fabrics.index') }}" class="btn btn-light border text-muted" title="Reset Filter">
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
                    <th style="white-space: nowrap;">Kode</th>
                    <th style="white-space: nowrap;">Nama Kain</th>
                    <th style="white-space: nowrap;">Kategori</th>
                    <th style="white-space: nowrap;">Warna</th>
                    <th class="text-end" style="white-space: nowrap;">Harga / Meter</th>
                    <th class="text-end" style="white-space: nowrap;">Harga / Rol</th>
                    <th class="text-end" style="white-space: nowrap;">Stok Rol</th>
                    <th class="text-end" style="white-space: nowrap;">Stok Meter</th>
                    <th class="text-center" style="white-space: nowrap;">Status</th>
                    <th class="text-center" style="white-space: nowrap;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($fabrics as $fabric)
                @php $s = $fabric->stock; @endphp
                <tr>
                    <td style="white-space: nowrap;"><span class="dt-badge dt-badge-navy">{{ $fabric->kode_kain }}</span></td>
                    <td style="white-space: nowrap;">
                        <div class="fw-600 text-navy">{{ $fabric->nama_kain }}</div>
                        <div style="font-size:11px;color:var(--dt-muted)">{{ $fabric->jenis_kain }}</div>
                    </td>
                    <td style="white-space: nowrap;">{{ $fabric->category->nama_kategori }}</td>
                    <td style="white-space: nowrap;">{{ $fabric->warna }}</td>
                    <td class="text-end fw-600 text-navy" style="white-space: nowrap;">Rp {{ number_format($fabric->harga_per_meter,0,',','.') }}</td>
                    <td class="text-end fw-600 text-muted" style="white-space: nowrap;">Rp {{ number_format($fabric->harga_per_rol,0,',','.') }}</td>
                    <td class="text-end fw-700 text-navy" style="white-space: nowrap;">{{ $s?->stok_rol ?? 0 }} rol</td>
                    <td class="text-end fw-600 text-navy" style="white-space: nowrap;">{{ number_format($fabric->total_stok_meter, 1) }} m</td>
                    <td class="text-center" style="white-space: nowrap;">
                        @if($fabric->status_stok === 'habis')
                            <span class="badge-stok-habis"><i class="bi bi-x-circle-fill me-1"></i> Habis</span>
                        @elseif($fabric->status_stok === 'menipis')
                            <span class="badge-stok-menipis"><i class="bi bi-exclamation-triangle-fill me-1"></i> Stok Menipis</span>
                        @else
                            <span class="badge-stok-aman"><i class="bi bi-check-circle-fill me-1"></i> Stok Aman</span>
                        @endif
                    </td>
                    <td class="text-center" style="white-space: nowrap;">
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('admin.fabrics.show', $fabric) }}">
                                    <i class="bi bi-eye me-1.5"></i> Detail
                                </a>
                                <a href="{{ route('admin.fabrics.edit', $fabric) }}">
                                    <i class="bi bi-pencil me-1.5"></i> Edit
                                </a>
                                <div class="dt-menu-divider"></div>
                                <form method="POST" action="{{ route('admin.fabrics.destroy', $fabric) }}" onsubmit="return confirm('Hapus kain {{ $fabric->nama_kain }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dt-menu-danger">
                                        <i class="bi bi-trash me-1.5"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-4 d-block mb-2 opacity-50"></i>Tidak ada data kain</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top bg-light bg-opacity-30">{{ $fabrics->links() }}</div>
</div>
@endsection
