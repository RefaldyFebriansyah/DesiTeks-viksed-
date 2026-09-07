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
        <table class="dt-table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Supplier</th>
                    <th>Email</th>
                    <th>Asal Kota</th>
                    <th>No. Telepon (+62)</th>
                    <th>Alamat</th>
                    <th>Barang Masuk</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($suppliers as $sup)
                <tr>
                    <td style="white-space: nowrap;"><span class="dt-badge dt-badge-navy">{{ $sup->kode_supplier }}</span></td>
                    <td class="fw-600 text-navy" style="white-space: nowrap;">{{ $sup->nama_supplier }}</td>
                    <td style="white-space: nowrap;">
                        @if($sup->email)
                            <div class="d-flex align-items-center gap-2 email-wrap flex-nowrap" style="white-space: nowrap;">
                                <span class="email-val text-navy fw-500 text-nowrap" style="font-size: 13px; white-space: nowrap;" data-real="{{ $sup->email }}" data-masked="{{ $sup->masked_email }}">{{ $sup->email }}</span>
                                <button type="button" class="btn btn-link p-0 text-muted border-0 flex-shrink-0" onclick="toggleEmail(this)" title="Sensor / Buka Sensor Email" style="line-height:1;">
                                    <i class="bi bi-eye-slash text-secondary" style="font-size: 13px;"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;">
                        @if($sup->asal_kota)
                            <span class="dt-badge dt-badge-secondary"><i class="bi bi-geo-alt me-1"></i>{{ $sup->asal_kota }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="white-space: nowrap;">
                        @if($sup->no_telepon)
                            <a href="https://wa.me/{{ preg_replace('/[^\d]/', '', $sup->no_telepon) }}" target="_blank" class="text-decoration-none text-success fw-500" style="white-space: nowrap;">
                                <i class="bi bi-whatsapp me-1"></i>{{ $sup->no_telepon }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $sup->alamat ?? '-' }}</td>
                    <td style="white-space: nowrap;"><span class="dt-badge dt-badge-gold">{{ $sup->incoming_goods_count }} Transaksi</span></td>
                    <td class="text-center" style="white-space: nowrap;">
                        <div class="dt-action-wrap">
                            <button class="dt-action-btn" onclick="toggleMenu(this)" type="button">⋮</button>
                            <div class="dt-action-menu">
                                <a href="{{ route('admin.suppliers.edit', $sup) }}">
                                    <i class="bi bi-pencil me-1.5"></i> Edit
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
                <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada supplier yang ditemukan.</td></tr>
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
