@extends('layouts.app')
@section('title', 'Data Kain')
@section('page-title', 'Data Kain')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Data Kain</h1>
        <div class="dt-breadcrumb">Master Data / Kain</div>
    </div>
    <a href="{{ route('admin.fabrics.create') }}" class="dt-btn dt-btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Kain
    </a>
</div>

{{-- Filter --}}
<div class="dt-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-4">
            <input type="text" name="search" value="{{ request('search') }}" class="dt-input" placeholder="Cari kode / nama / warna...">
        </div>
        <div class="col-sm-3">
            <select name="category_id" class="dt-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id ? 'selected':'' }}>{{ $cat->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-2">
            <select name="status" class="dt-select">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                <option value="nonaktif" {{ request('status')=='nonaktif'?'selected':'' }}>Nonaktif</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="dt-btn dt-btn-primary"><i class="bi bi-search"></i> Filter</button>
            @if(request()->hasAny(['search','category_id','status']))
                <a href="{{ route('admin.fabrics.index') }}" class="dt-btn dt-btn-outline"><i class="bi bi-x"></i> Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="dt-card">
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>Kode</th><th>Nama Kain</th><th>Kategori</th><th>Warna</th>
                    <th>Harga/Meter</th><th>Harga/Rol</th><th>Stok Rol</th><th>Stok Meter</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($fabrics as $fabric)
                @php $s = $fabric->stock; @endphp
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $fabric->kode_kain }}</span></td>
                    <td><div class="fw-600">{{ $fabric->nama_kain }}</div><div style="font-size:11px;color:var(--dt-muted)">{{ $fabric->jenis_kain }}</div></td>
                    <td>{{ $fabric->category->nama_kategori }}</td>
                    <td>{{ $fabric->warna }}</td>
                    <td>Rp {{ number_format($fabric->harga_per_meter,0,',','.') }}</td>
                    <td>Rp {{ number_format($fabric->harga_per_rol,0,',','.') }}</td>
                    <td>{{ $s?->stok_rol ?? 0 }}</td>
                    <td>{{ number_format($s?->stok_meter ?? 0, 1) }}</td>
                    <td>
                        @php
                            $statusStok = $fabric->status_stok;
                            $badgeClass = match($statusStok) { 'habis'=>'dt-badge-danger','menipis'=>'dt-badge-warning', default=>'dt-badge-success' };
                        @endphp
                        <span class="dt-badge {{ $badgeClass }}">{{ ucfirst($statusStok) }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.fabrics.show', $fabric) }}" class="dt-btn dt-btn-outline dt-btn-xs">Detail</a>
                            <a href="{{ route('admin.fabrics.edit', $fabric) }}" class="dt-btn dt-btn-primary dt-btn-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.fabrics.destroy', $fabric) }}" onsubmit="return confirm('Hapus kain {{ $fabric->nama_kain }}?')">
                                @csrf @method('DELETE')
                                <button class="dt-btn dt-btn-danger dt-btn-xs">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center py-5" style="color:var(--dt-muted)"><i class="bi bi-inbox fs-4 d-block mb-2"></i>Tidak ada data kain</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $fabrics->links() }}</div>
</div>
@endsection
