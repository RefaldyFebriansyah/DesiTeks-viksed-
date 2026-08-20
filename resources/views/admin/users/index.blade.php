@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('page-title', 'Pengguna Sistem')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Manajemen Pengguna</h1>
        <div class="dt-breadcrumb">Sistem / Pengguna</div>
    </div>
    <a href="{{ route('admin.users.create') }}" class="dt-btn dt-btn-primary">
        <i class="bi bi-person-plus"></i> Tambah Pengguna
    </a>
</div>

<div class="dt-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-4">
            <input type="text" name="search" value="{{ request('search') }}" class="dt-input" placeholder="Cari nama atau username...">
        </div>
        <div class="col-sm-3">
            <select name="role" class="dt-select">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
                <option value="gudang" {{ request('role')=='gudang'?'selected':'' }}>Gudang</option>
                <option value="kasir" {{ request('role')=='kasir'?'selected':'' }}>Kasir</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="dt-btn dt-btn-primary"><i class="bi bi-search"></i> Filter</button>
            @if(request()->hasAny(['search','role']))
                <a href="{{ route('admin.users.index') }}" class="dt-btn dt-btn-outline"><i class="bi bi-x"></i> Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="dt-card">
    <div class="dt-table-wrap">
        <table class="dt-table">
            <thead>
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td class="fw-600 text-navy">{{ $u->name }}</td>
                    <td><span class="dt-badge dt-badge-navy">{{ $u->username }}</span></td>
                    <td><span class="dt-badge dt-badge-gold">{{ ucfirst($u->role) }}</span></td>
                    <td>
                        <span class="dt-badge {{ $u->status === 'aktif' ? 'dt-badge-success' : 'dt-badge-danger' }}">
                            {{ ucfirst($u->status) }}
                        </span>
                    </td>
                    <td>{{ $u->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('admin.users.edit', $u) }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                @if($u->id !== auth()->id())
                                    <div class="dt-menu-divider"></div>
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus pengguna {{ $u->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dt-menu-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data pengguna.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
</div>
@endsection
