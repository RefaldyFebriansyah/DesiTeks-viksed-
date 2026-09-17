@extends('layouts.app')
@section('title', 'Stok Kain')
@section('page-title', 'Stok Kain & Penyesuaian')

@section('content')
<div class="dt-page-header mb-3">
    <div>
        <h1 class="dt-page-title">Stok Kain & Penyesuaian</h1>
        <div class="dt-breadcrumb">Gudang / Stok</div>
    </div>
</div>

<div class="dt-card border-0 shadow-sm mb-4" style="border-radius: 14px;">
    <div class="p-3 bg-white d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-radius: 14px;">
        {{-- Segmented Tabs --}}
        <div class="dt-filter-tabs">
            <a href="{{ route('admin.stocks.index', array_merge(request()->except('status'), ['status' => ''])) }}" 
               class="dt-tab-link {{ !request('status') ? 'active' : '' }}">
               Semua <span class="dt-tab-count">{{ $totalSemua ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.stocks.index', array_merge(request()->except('status'), ['status' => 'habis'])) }}" 
               class="dt-tab-link {{ request('status') === 'habis' ? 'active' : '' }}">
               Stok Habis <span class="dt-tab-count" style="background:#fee2e2;color:#dc2626;">{{ $countHabis ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.stocks.index', array_merge(request()->except('status'), ['status' => 'menipis'])) }}" 
               class="dt-tab-link {{ request('status') === 'menipis' ? 'active' : '' }}">
               Stok Menipis <span class="dt-tab-count warning">{{ $countMenipis ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.stocks.index', array_merge(request()->except('status'), ['status' => 'penuh'])) }}" 
               class="dt-tab-link {{ request('status') === 'penuh' ? 'active' : '' }}">
               Stok Penuh <span class="dt-tab-count" style="background:#fee2e2;color:#dc2626;">{{ $countPenuh ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.stocks.index', array_merge(request()->except('status'), ['status' => 'aman'])) }}" 
               class="dt-tab-link {{ request('status') === 'aman' ? 'active' : '' }}">
               Stok Aman <span class="dt-tab-count success">{{ $countAman ?? 0 }}</span>
            </a>
        </div>

        {{-- Integrated Search Input Bar --}}
        <form method="GET" action="{{ route('admin.stocks.index') }}" id="searchForm" class="d-flex gap-2 align-items-center dt-search-form" style="max-width: 320px; flex: 1;">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
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
                    <a href="{{ route('admin.stocks.index', request('status') ? ['status' => request('status')] : []) }}" class="btn btn-light border border-start-0 text-muted" title="Reset"><i class="bi bi-x-circle-fill"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <div class="dt-table-wrap">
        <table class="dt-table mb-0 align-middle">
            <thead>
                <tr>
                    <th style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Kode</th>
                    <th style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Nama Kain</th>
                    <th style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Kategori</th>
                    <th class="text-end" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Stok Rol</th>
                    <th class="text-end" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Stok Meter</th>
                    <th class="text-end" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Max Stock Gudang</th>
                    <th class="text-center" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Status Stok</th>
                    <th style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Terakhir Update</th>
                    <th class="text-center" style="width: 70px; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Aksi</th>
                </tr>
            </thead>
            <tbody id="stockTableBody">
            @forelse($stocks as $stock)
                @php
                    $totalMeter = $stock->total_meter;
                    $maxStock   = $stock->fabric->stok_maksimum ?? 25;
                    $isOverstock = $maxStock > 0 && $stock->stok_rol >= $maxStock;
                @endphp
                <tr>
                    <td><span class="dt-badge dt-badge-navy">{{ $stock->fabric->kode_kain }}</span></td>
                    <td class="fw-600 text-navy">{{ $stock->fabric->nama_kain }}</td>
                    <td class="text-muted">{{ $stock->fabric->category->nama_kategori ?? '-' }}</td>
                    <td class="fw-700 text-end text-navy">{{ $stock->stok_rol }} rol</td>
                    <td class="fw-600 text-end text-navy">
                        {{ number_format($totalMeter, 1) }} m
                        @if($stock->stok_meter > 0)
                            <div style="font-size: 11px; color: var(--dt-muted); font-weight: normal;">(eceran {{ number_format($stock->stok_meter, 1) }}m)</div>
                        @endif
                    </td>
                    <td class="text-end fw-600 {{ $isOverstock ? 'text-danger' : 'text-navy' }}">
                        {{ number_format($maxStock, 0) }} rol
                        @if($isOverstock)
                            <div style="font-size: 10.5px; color: #dc2626; font-weight: 700;"><i class="bi bi-exclamation-circle-fill me-1"></i>Penuh / Over</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($isOverstock)
                            <span class="badge-stok-over"><i class="bi bi-exclamation-triangle-fill me-1"></i> Stok Penuh</span>
                        @elseif($stock->status === 'habis')
                            <span class="badge-stok-habis"><i class="bi bi-x-circle-fill me-1"></i> Stok Habis</span>
                        @elseif($stock->status === 'menipis')
                            <span class="badge-stok-menipis"><i class="bi bi-exclamation-triangle-fill me-1"></i> Stok Menipis</span>
                        @else
                            <span class="badge-stok-aman"><i class="bi bi-check-circle-fill me-1"></i> Stok Aman</span>
                        @endif
                    </td>
                    <td><small class="text-muted">{{ $stock->updated_at ? $stock->updated_at->diffForHumans() : '-' }}</small></td>
                    <td class="text-center" style="white-space: nowrap;">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm border rounded-3 p-0 d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 36px; height: 36px; border-color: #e2e8f0 !important; background: #ffffff; color: #475569;" title="Menu Aksi">
                                <i class="bi bi-three-dots-vertical fs-6"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size: 13.5px;">
                                <li>
                                    <button type="button" class="dropdown-item py-2" data-bs-toggle="modal" data-bs-target="#maxStockModal{{ $stock->fabric_id }}">
                                        <i class="bi bi-diagram-3 me-2 text-warning"></i> Atur Max Stock Gudang
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item py-2" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $stock->fabric_id }}">
                                        <i class="bi bi-sliders me-2 text-primary"></i> Penyesuaian Stok
                                    </button>
                                </li>
                            </ul>
                        </div>

                        {{-- Modal Atur Max Stock --}}
                        <div class="modal fade text-start" id="maxStockModal{{ $stock->fabric_id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header text-white" style="background: var(--dt-navy);">
                                        <h5 class="modal-title fs-6 fw-600"><i class="bi bi-diagram-3 me-2 text-warning"></i> Atur Max Stock Gudang: {{ $stock->fabric->nama_kain }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.stocks.update-max-stock', $stock->fabric_id) }}">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <div class="alert alert-info py-2 px-3 mb-3 border-0 d-flex align-items-center gap-2" style="font-size: 12.5px; background: #e0f2fe; color: #0369a1;">
                                                <i class="bi bi-info-circle-fill fs-6 flex-shrink-0"></i>
                                                <div>Stok rol saat ini: <strong>{{ $stock->stok_rol }} rol</strong> ({{ number_format($totalMeter, 1) }}m). Tentukan kapasitas maksimum stok gudang dalam rol.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="dt-label mb-1">Max Stock Gudang (Rol)</label>
                                                <input type="number" name="stok_maksimum" class="dt-input" value="{{ old('stok_maksimum', $maxStock) }}" required min="0" placeholder="Contoh: 25">
                                                <div class="form-text text-muted" style="font-size: 11.5px;">Batas maksimal stok kain yang dapat ditampung di gudang (dalam jumlah rol).</div>
                                            </div>
                                            <div class="form-check p-3 bg-light rounded-3 border">
                                                <input class="form-check-input" type="checkbox" name="terapkan_ke_semua" value="1" id="applyAll{{ $stock->fabric_id }}">
                                                <label class="form-check-label fw-600 text-navy" for="applyAll{{ $stock->fabric_id }}" style="font-size: 12.5px; cursor: pointer;">
                                                    <i class="bi bi-check2-all text-primary me-1"></i> Terapkan batas max stock ini ke SELURUH item kain di gudang
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-top-0 px-4 py-3">
                                            <button type="button" class="dt-btn dt-btn-outline" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="dt-btn dt-btn-primary px-4">Simpan Max Stock</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Adjust --}}
                        <div class="modal fade text-start" id="adjustModal{{ $stock->fabric_id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header text-white" style="background: var(--dt-navy);">
                                        <h5 class="modal-title fs-6 fw-600"><i class="bi bi-sliders me-2 text-gold"></i> Penyesuaian Stok: {{ $stock->fabric->nama_kain }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.stocks.adjust', $stock->fabric_id) }}">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="dt-label mb-1">Stok Rol Baru</label>
                                                <input type="number" name="stok_rol" class="dt-input" value="{{ $stock->stok_rol }}" required min="0">
                                            </div>
                                            <div class="mb-3">
                                                <label class="dt-label mb-1">Stok Meter Baru (Sisa Eceran)</label>
                                                <input type="number" step="0.01" name="stok_meter" class="dt-input" value="{{ $stock->stok_meter }}" required min="0">
                                            </div>
                                            <div class="mb-3">
                                                <label class="dt-label mb-1">Alasan Penyesuaian</label>
                                                <input type="text" name="keterangan" class="dt-input" placeholder="Misal: Hasil stok opname / barang rusak" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-top-0 px-4 py-3">
                                            <button type="button" class="dt-btn dt-btn-outline" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="dt-btn dt-btn-primary px-4">Simpan Penyesuaian</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">Data stok kain tidak ditemukan.</td>
                </tr>
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

    // Instant 0ms DOM row filter
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

        // Dynamic empty state
        let noResultRow = document.getElementById('noResultRow');
        if (visibleCount === 0 && query !== '') {
            if (!noResultRow) {
                noResultRow = document.createElement('tr');
                noResultRow.id = 'noResultRow';
                const colCount = tableBody.previousElementSibling?.querySelectorAll('th').length || 8;
                noResultRow.innerHTML = `<td colspan="${colCount}" class="text-center py-4 text-muted">Tidak ada data stok yang cocok dengan "${searchInput.value}"</td>`;
                tableBody.appendChild(noResultRow);
            }
        } else if (noResultRow) {
            noResultRow.remove();
        }

        // Hide pagination while searching locally
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
