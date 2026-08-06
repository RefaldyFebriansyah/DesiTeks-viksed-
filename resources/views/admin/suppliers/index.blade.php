@extends('layouts.app')
@section('title', 'Data Supplier')
@section('page-title', 'Data Supplier')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Supplier</h1>
        <div class="dt-breadcrumb">Master Data / Supplier</div>
    </div>
    <a href="{{ route('admin.suppliers.create') }}" class="dt-btn dt-btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Supplier
    </a>
</div>

<div class="dt-card">
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Supplier</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>Barang Masuk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($suppliers as $sup)
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $sup->kode_supplier }}</span></td>
                    <td class="fw-600 text-navy">{{ $sup->nama_supplier }}</td>
                    <td>{{ $sup->no_telepon ?? '-' }}</td>
                    <td>{{ $sup->alamat ?? '-' }}</td>
                    <td><span class="dt-badge dt-badge-gold">{{ $sup->incoming_goods_count }} Transaksi</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.suppliers.edit', $sup) }}" class="dt-btn dt-btn-primary dt-btn-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.suppliers.destroy', $sup) }}" onsubmit="return confirm('Hapus supplier {{ $sup->nama_supplier }}?')">
                                @csrf @method('DELETE')
                                <button class="dt-btn dt-btn-danger dt-btn-xs">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada supplier.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $suppliers->links() }}</div>
</div>
@endsection
