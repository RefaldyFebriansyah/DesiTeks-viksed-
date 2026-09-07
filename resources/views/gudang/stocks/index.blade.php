@extends('layouts.app')
@section('title', 'Stok Kain Gudang')
@section('page-title', 'Data Stok Kain')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Stok Kain</h1>
        <div class="dt-breadcrumb">Gudang / Stok</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <!-- Integrated Search Toolbar -->
    <div class="p-3 bg-white border-bottom">
        <form method="GET" action="{{ route('gudang.stocks.index') }}" id="searchForm" class="d-flex align-items-center gap-2" style="max-width: 420px;">
            <div class="input-group min-w-0">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" 
                       name="search" 
                       id="searchInput"
                       value="{{ request('search') }}" 
                       class="form-control border-start-0 ps-0 bg-light" 
                       placeholder="Cari kode atau nama kain..." 
                       style="font-size: 13.5px;" 
                       autocomplete="off">
                @if(request('search'))
                    <a href="{{ route('gudang.stocks.index') }}" class="btn btn-light border border-start-0 text-muted" title="Reset"><i class="bi bi-x-circle-fill"></i></a>
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
                    <th class="text-end" style="white-space: nowrap;">Stok Rol</th>
                    <th class="text-end" style="white-space: nowrap;">Total Meter</th>
                    <th class="text-center" style="white-space: nowrap;">Status</th>
                </tr>
            </thead>
            <tbody id="stockTableBody">
            @forelse($stocks as $stock)
                <tr>
                    <td style="white-space: nowrap;"><span class="dt-badge dt-badge-navy">{{ $stock->fabric->kode_kain }}</span></td>
                    <td style="white-space: nowrap;" class="fw-600 text-navy">{{ $stock->fabric->nama_kain }}</td>
                    <td style="white-space: nowrap;">{{ $stock->fabric->category->nama_kategori }}</td>
                    <td class="text-end fw-700 text-navy" style="white-space: nowrap;">{{ $stock->stok_rol }} rol</td>
                    <td class="text-end fw-600 text-navy" style="white-space: nowrap;">
                        {{ number_format($stock->total_meter, 1) }} m
                        @if($stock->stok_meter > 0)
                            <div style="font-size: 11px; color: var(--dt-muted); font-weight: normal; white-space: nowrap;">(eceran {{ number_format($stock->stok_meter, 1) }}m)</div>
                        @endif
                    </td>
                    <td class="text-center" style="white-space: nowrap;">
                        @if($stock->status === 'habis')
                            <span class="badge-stok-habis"><i class="bi bi-x-circle-fill me-1"></i> Habis</span>
                        @elseif($stock->status === 'menipis')
                            <span class="badge-stok-menipis"><i class="bi bi-exclamation-triangle-fill me-1"></i> Stok Menipis</span>
                        @else
                            <span class="badge-stok-aman"><i class="bi bi-check-circle-fill me-1"></i> Stok Aman</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data stok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div id="paginationContainer">
        @if(method_exists($stocks, 'links'))
            <div class="p-3 border-top bg-light bg-opacity-30">{{ $stocks->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchForm  = document.getElementById('searchForm');
    const tableBody   = document.getElementById('stockTableBody');
    const pagination  = document.getElementById('paginationContainer');

    if (!searchInput || !tableBody) return;

    let timer = null;
    let controller = null;

    function filterTable() {
        const query = searchInput.value.toLowerCase().trim();
        const rows = tableBody.querySelectorAll('tr:not(#noResultRow)');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (!query || text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        let noResultRow = document.getElementById('noResultRow');
        if (visibleCount === 0 && query !== '') {
            if (!noResultRow) {
                noResultRow = document.createElement('tr');
                noResultRow.id = 'noResultRow';
                const colCount = tableBody.previousElementSibling?.querySelectorAll('th').length || 6;
                noResultRow.innerHTML = `<td colspan="${colCount}" class="text-center py-4 text-muted">Tidak ada data stok yang cocok dengan "${searchInput.value}"</td>`;
                tableBody.appendChild(noResultRow);
            }
        } else if (noResultRow) {
            noResultRow.remove();
        }

        if (pagination) {
            pagination.style.display = query ? 'none' : '';
        }
    }

    searchInput.addEventListener('input', () => {
        filterTable();
    });
});
</script>
@endpush
@endsection
