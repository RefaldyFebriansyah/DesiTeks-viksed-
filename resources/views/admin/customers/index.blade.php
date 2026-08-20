@extends('layouts.app')
@section('title', 'Kelola Pelanggan')
@section('page-title', 'Daftar Pelanggan')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Kelola Pelanggan & CRM</h1>
        <div class="dt-breadcrumb">Master Data / Pelanggan</div>
    </div>
    <a href="{{ route('admin.customers.create') }}" class="dt-btn dt-btn-primary">
        <i class="bi bi-person-plus"></i> Tambah Pelanggan
    </a>
</div>

{{-- Filter Card --}}
<div class="dt-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="dt-label">Cari Pelanggan</label>
            <div class="position-relative">
                <i class="bi bi-search position-absolute text-muted" style="left:12px; top:50%; transform:translateY(-50%)"></i>
                <input type="text" name="search" value="{{ request('search') }}" class="dt-input ps-5" placeholder="Masukkan nama, kode, atau nomor telepon...">
            </div>
        </div>
        <div class="col-md-3">
            <label class="dt-label">Tipe Pelanggan</label>
            <select name="tipe" class="dt-select">
                <option value="">Semua Tipe</option>
                <option value="eceran" {{ request('tipe') == 'eceran' ? 'selected' : '' }}>Eceran</option>
                <option value="grosir" {{ request('tipe') == 'grosir' ? 'selected' : '' }}>Grosir</option>
                <option value="member" {{ request('tipe') == 'member' ? 'selected' : '' }}>Member</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="dt-btn dt-btn-primary flex-fill justify-content-center"><i class="bi bi-filter"></i> Terapkan</button>
            @if(request()->hasAny(['search', 'tipe']))
                <a href="{{ route('admin.customers.index') }}" class="dt-btn dt-btn-outline"><i class="bi bi-x-circle"></i> Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Data Table --}}
<div class="dt-card">
    @if($customers->isEmpty())
        <div class="empty-state py-5">
            <i class="bi bi-people fs-1" style="opacity: .4"></i>
            <h5 class="mt-3">Belum ada data pelanggan</h5>
            <p class="text-muted">Tambahkan pelanggan untuk melacak transaksi mereka dan mengaktifkan program member diskon.</p>
        </div>
    @else
        <div class="dt-table-wrap">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Pelanggan</th>
                        <th>No. Telepon</th>
                        <th>Alamat</th>
                        <th>Tipe</th>
                        <th>Diskon Member</th>
                        <th class="text-end" style="width: 80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $c)
                        @php
                            $badgeClass = match($c->tipe) {
                                'member' => 'dt-badge-success',
                                'grosir' => 'dt-badge-gold',
                                default => 'dt-badge-navy'
                            };
                        @endphp
                        <tr>
                            <td><span class="dt-badge dt-badge-navy">{{ $c->kode_pelanggan }}</span></td>
                            <td class="fw-600 text-navy">{{ $c->nama }}</td>
                            <td>{{ $c->telepon ?? '-' }}</td>
                            <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $c->alamat ?? '-' }}
                            </td>
                            <td><span class="dt-badge {{ $badgeClass }}">{{ ucfirst($c->tipe) }}</span></td>
                            <td>
                                @if($c->tipe === 'member')
                                    <strong class="text-success">{{ number_format($c->diskon_member, 1) }}%</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="dt-action-wrap">
                                    <button class="dt-action-btn" onclick="toggleMenu(this)"><i class="bi bi-three-dots-vertical"></i></button>
                                    <div class="dt-action-menu">
                                        <a href="{{ route('admin.customers.edit', $c) }}"><i class="bi bi-pencil text-primary"></i> Edit Data</a>
                                        <div class="dt-menu-divider"></div>
                                        <form method="POST" action="{{ route('admin.customers.destroy', $c) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dt-menu-danger"><i class="bi bi-trash text-danger"></i> Hapus Pelanggan</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <span style="font-size:12.5px; color:var(--dt-muted)">
                Menampilkan {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} dari {{ $customers->total() }} pelanggan
            </span>
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
