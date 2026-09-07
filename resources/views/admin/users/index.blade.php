@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('page-title', 'Pengguna Sistem')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Manajemen Pengguna</h1>
        <div class="dt-breadcrumb">Sistem / Pengguna</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <!-- Unified Toolbar: Integrated Search, Role Filter & Action Button -->
    <div class="p-3 bg-white border-bottom">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group min-w-0">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 bg-light" placeholder="Ketik nama atau username..." value="{{ request('search') }}" style="font-size: 13.5px;" onchange="this.form.submit()">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="role" class="form-select bg-light" style="font-size: 13.5px;" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
                    <option value="gudang" {{ request('role')=='gudang'?'selected':'' }}>Gudang</option>
                    <option value="kasir" {{ request('role')=='kasir'?'selected':'' }}>Kasir</option>
                </select>
            </div>
            <div class="col-12 col-md-4 text-md-end ms-auto d-flex align-items-center justify-content-end gap-2">
                @if(request()->hasAny(['search','role']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light border text-muted" title="Reset Filter">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                @endif
                <a href="{{ route('admin.users.create') }}" class="dt-btn dt-btn-primary flex-shrink-0">
                    <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="dt-table-wrap">
        <table class="dt-table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th class="text-center">Aksi</th>
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
                    <td class="text-center">
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('admin.users.edit', $u) }}">
                                    <i class="bi bi-pencil me-1.5"></i> Edit
                                </a>
                                @if($u->id !== auth()->id())
                                    <div class="dt-menu-divider"></div>
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus pengguna {{ $u->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dt-menu-danger">
                                            <i class="bi bi-trash me-1.5"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data pengguna.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top bg-light bg-opacity-30">{{ $users->links() }}</div>
</div>
@endsection
