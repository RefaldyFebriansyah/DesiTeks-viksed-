@extends('layouts.app')
@section('title', 'Kategori Kain')
@section('page-title', 'Kategori Kain')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Kategori Kain</h1>
        <div class="dt-breadcrumb">Master Data / Kategori</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <!-- Combined Toolbar: Integrated Action Header -->
    <div class="p-3 bg-white border-bottom d-flex align-items-center justify-content-between">
        <div class="fw-700 text-navy" style="font-size: 14px;"><i class="bi bi-tags me-1.5 text-primary"></i> Daftar Kategori Kain</div>
        <a href="{{ route('admin.categories.create') }}" class="dt-btn dt-btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
        </a>
    </div>

    <!-- Table -->
    <div class="dt-table-wrap">
        <table class="dt-table mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Kain</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($categories as $index => $cat)
                <tr>
                    <td>{{ $categories->firstItem() + $index }}</td>
                    <td class="fw-600 text-navy">{{ $cat->nama_kategori }}</td>
                    <td>{{ $cat->deskripsi ?? '-' }}</td>
                    <td><span class="dt-badge dt-badge-navy">{{ $cat->fabrics_count }} Kain</span></td>
                    <td class="text-center">
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('admin.categories.edit', $cat) }}">
                                    <i class="bi bi-pencil me-1.5"></i> Edit
                                </a>
                                <div class="dt-menu-divider"></div>
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori {{ $cat->nama_kategori }}?')">
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
                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada kategori kain.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top bg-light bg-opacity-30">{{ $categories->links() }}</div>
</div>
@endsection
