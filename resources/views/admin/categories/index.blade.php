@extends('layouts.app')
@section('title', 'Kategori Kain')
@section('page-title', 'Kategori Kain')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Kategori Kain</h1>
        <div class="dt-breadcrumb">Master Data / Kategori</div>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="dt-btn dt-btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Kategori
    </a>
</div>

<div class="dt-card">
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Kain</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($categories as $index => $cat)
                <tr>
                    <td>{{ $categories->firstItem() + $index }}</td>
                    <td class="fw-600 text-navy">{{ $cat->nama_kategori }}</td>
                    <td>{{ $cat->deskripsi ?? '-' }}</td>
                    <td><span class="dt-badge dt-badge-navy">{{ $cat->fabrics_count }} Kain</span></td>
                    <td>
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('admin.categories.edit', $cat) }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <div class="dt-menu-divider"></div>
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori {{ $cat->nama_kategori }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dt-menu-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada kategori kain.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $categories->links() }}</div>
</div>
@endsection
