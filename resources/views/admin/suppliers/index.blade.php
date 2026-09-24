@extends('layouts.app')
@section('title', 'Data Supplier')
@section('page-title', 'Data Supplier')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Supplier</h1>
        <div class="dt-breadcrumb">Master Data / Supplier</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <!-- Unified Toolbar: Single Search Bar & Action Button -->
    <div class="p-3 bg-white border-bottom">
        <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-3">
            <form method="GET" action="{{ route('admin.suppliers.index') }}" class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 520px;">
                <div class="input-group min-w-0">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="searchInput" autocomplete="off" class="form-control border-start-0 ps-0 bg-light" placeholder="Ketik nama supplier, email, kota, telepon..." value="{{ request('search') }}" style="font-size: 13.5px;">
                    @if(request('search'))
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light border border-start-0 text-muted" title="Reset"><i class="bi bi-x-circle-fill"></i></a>
                    @endif
                </div>
            </form>

            <a href="{{ route('admin.suppliers.create') }}" class="dt-btn dt-btn-primary flex-shrink-0 ms-md-auto">
                <i class="bi bi-plus-lg me-1"></i> Tambah Supplier
            </a>
        </div>
    </div>

    <div class="dt-table-wrap">
        <table class="dt-table mb-0 align-middle" style="width: 100%; table-layout: fixed;">
            <thead>
                <tr>
                    <th style="width: 215px;">Nama Supplier</th>
                    <th style="width: 170px;">Email</th>
                    <th style="width: 115px;">Asal Kota</th>
                    <th style="width: 165px;">No. Telepon / WA</th>
                    <th>Alamat</th>
                    <th style="width: 120px;">Barang Masuk</th>
                    <th class="text-center" style="width: 50px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($suppliers as $sup)
                @php
                    $cleanName = trim(preg_replace('/^(PT|CV|UD)\.?\s*/i', '', $sup->nama_supplier));
                    $initials = strtoupper(substr($cleanName, 0, min(2, strlen($cleanName))));
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center" style="gap: 12px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-navy fw-bold flex-shrink-0" style="width: 36px; height: 36px; background: #e2e8f0; font-size: 12px; border: 1px solid #cbd5e1; letter-spacing: 0.5px;">
                                {{ $initials ?: 'SP' }}
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-600 text-navy dt-truncate" title="{{ $sup->nama_supplier }}" style="font-size: 13.5px; max-width: 155px;">{{ $sup->nama_supplier }}</div>
                                <div class="mt-0.5"><span class="dt-badge dt-badge-navy" style="font-size: 9.5px; padding: 1px 6px; letter-spacing: 0.3px;">{{ $sup->kode_supplier }}</span></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($sup->email)
                            <span class="text-navy fw-500 dt-truncate" title="{{ $sup->email }}" style="font-size: 13px; max-width: 160px;">{{ $sup->email }}</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($sup->asal_kota)
                            <span class="dt-badge dt-badge-secondary dt-truncate d-inline-flex align-items-center gap-1" title="{{ $sup->asal_kota }}" style="max-width: 105px; font-size: 11.5px;">
                                <i class="bi bi-geo-alt text-secondary opacity-75"></i>
                                <span>{{ $sup->asal_kota }}</span>
                            </span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($sup->no_telepon)
                            <span class="text-dark fw-500" style="white-space: nowrap; font-size: 13px;">{{ $sup->formatted_phone }}</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-dark small dt-truncate" title="{{ $sup->alamat ?? '-' }}" style="max-width: 100%;">{{ $sup->alamat ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="dt-badge dt-badge-gold" style="font-size: 11.5px;">{{ $sup->incoming_goods_count }} Transaksi</span>
                    </td>
                    <td class="text-center">
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('admin.suppliers.edit', $sup) }}">
                                    <i class="bi bi-pencil me-1.5 text-primary"></i> Edit Supplier
                                </a>
                                <div class="dt-menu-divider"></div>
                                <form method="POST" action="{{ route('admin.suppliers.destroy', $sup) }}" onsubmit="return confirm('Hapus supplier {{ $sup->nama_supplier }}?')">
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
                <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada supplier yang ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top bg-light bg-opacity-30">{{ $suppliers->links() }}</div>
</div>

@push('scripts')
<script>
function toggleEmail(btn) {
    const wrap = btn.closest('.email-wrap');
    const span = wrap.querySelector('.email-val');
    const icon = btn.querySelector('i');
    if (span.textContent.trim() === span.dataset.real) {
        span.textContent = span.dataset.masked;
        span.classList.add('text-muted');
        span.classList.remove('text-navy', 'fw-500');
        icon.className = 'bi bi-eye text-primary';
        btn.title = 'Tampilkan Email Lengkap';
    } else {
        span.textContent = span.dataset.real;
        span.classList.remove('text-muted');
        span.classList.add('text-navy', 'fw-500');
        icon.className = 'bi bi-eye-slash text-secondary';
        btn.title = 'Sensor Email';
    }
}
</script>
@endpush
@endsection
