@extends('layouts.app')

@section('title', 'Surat Jalan Online Masuk')

@push('styles')
<style>
    /* Card Container */
    .dt-card-table {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    /* Header Controls */
    .search-filter-input {
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 13.5px;
        color: #0f172a;
        transition: all 0.15s ease;
    }
    .search-filter-input:focus {
        background-color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    /* Table Styling */
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    .table-modern thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 18px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-modern tbody td {
        padding: 14px 18px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .table-modern tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-modern tbody tr:hover {
        background-color: #f8fafc;
    }
    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status Pill with Dot Indicator */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.3;
        white-space: nowrap;
    }
    .status-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-warning { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    .status-warning .dot { background: #d97706; }

    .status-info    { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
    .status-info .dot    { background: #2563eb; }

    .status-success { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
    .status-success .dot { background: #16a34a; }

    .status-danger  { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
    .status-danger .dot  { background: #dc2626; }

    /* Action Button */
    .btn-action-dots {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    .btn-action-dots:hover, .btn-action-dots:focus {
        background-color: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    /* Responsive Mobile Card View */
    @media (max-width: 767.98px) {
        .table-responsive-wrapper {
            padding: 12px;
        }
        .table-modern, .table-modern thead, .table-modern tbody, .table-modern th, .table-modern td, .table-modern tr {
            display: block;
        }
        .table-modern thead {
            display: none;
        }
        .table-modern tbody tr {
            margin-bottom: 12px;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px;
            padding: 14px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .table-modern tbody td {
            border-bottom: none !important;
            padding: 6px 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-modern tbody td::before {
            content: attr(data-label);
            font-weight: 700;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .table-modern tbody td.text-end {
            text-align: right !important;
        }
    }
</style>
@endpush

@section('content')
<div class="pb-5">
    <!-- Header Title & Breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item"><a href="{{ route('gudang.dashboard') }}" class="text-decoration-none">Gudang</a></li>
                <li class="breadcrumb-item active">Surat Jalan Online</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.02em;">Surat Jalan Online & Stok Masuk</h3>
        <p class="text-secondary mb-0 small">Periksa pengiriman surat jalan digital dari mitra supplier sebelum memasukkan kain ke stok fisik gudang.</p>
    </div>

    <!-- Main Card Container -->
    <div class="dt-card-table">
        <!-- Top Search Bar & Filters -->
        <div class="p-3 border-bottom bg-white">
            <form action="{{ route(request()->routeIs('admin*') ? 'admin.delivery-orders.index' : 'gudang.delivery-orders.index') }}" method="GET" id="searchForm" onsubmit="event.preventDefault(); return false;">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5 col-lg-4">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" name="q" id="searchInput" class="form-control search-filter-input ps-5" placeholder="Cari No. Surat Jalan / Supplier / Supir..." value="{{ $search }}" autocomplete="off" oninput="handleLiveSearch(this)">
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <select name="status" class="form-select search-filter-input" onchange="window.location.href='{{ route(request()->routeIs('admin*') ? 'admin.delivery-orders.index' : 'gudang.delivery-orders.index') }}?status=' + this.value">
                            <option value="menunggu_approval" {{ $status === 'menunggu_approval' ? 'selected' : '' }}>Menunggu ACC Admin ({{ $counts['menunggu_approval'] }})</option>
                            <option value="disetujui_admin" {{ $status === 'disetujui_admin' ? 'selected' : '' }}>Disetujui Admin Toko</option>
                            <option value="dalam_perjalanan" {{ $status === 'dalam_perjalanan' ? 'selected' : '' }}>Dalam Perjalanan</option>
                            <option value="diterima" {{ $status === 'diterima' ? 'selected' : '' }}>Diterima Gudang ({{ $counts['diterima'] }})</option>
                            <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak ({{ $counts['ditolak'] }})</option>
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status ({{ $counts['all'] }})</option>
                        </select>
                    </div>

                    <div class="col-auto d-none" id="resetSearchBtn">
                        <button type="button" onclick="clearLiveSearch()" class="btn btn-light border rounded-2 text-secondary px-3 py-1.5 small">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Responsive Data Table View -->
        @if($deliveryOrders->count() > 0)
            <div class="table-responsive-wrapper">
                <table class="table-modern" id="deliveryTable">
                    <thead>
                        <tr>
                            <th class="ps-4">No. Surat Jalan</th>
                            <th>Mitra Supplier</th>
                            <th>Gudang & Armada</th>
                            <th class="text-end">Muatan Kain</th>
                            <th class="text-center">Status</th>
                            <th class="text-center pe-4" style="width: 50px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach($deliveryOrders as $order)
                            <tr class="table-row-item">
                                <td class="ps-4" data-label="No. Surat Jalan">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 bg-light d-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 32px; height: 32px; font-size: 14px;">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route(request()->routeIs('admin*') ? 'admin.delivery-orders.show' : 'gudang.delivery-orders.show', $order->id) }}" class="fw-bold text-dark text-decoration-none d-block" style="font-size: 13.5px;">
                                                {{ $order->nomor_surat_jalan }}
                                            </a>
                                            <div class="text-muted" style="font-size: 11px;">
                                                Tgl: {{ $order->tanggal_kirim ? $order->tanggal_kirim->format('d/m/Y') : $order->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Mitra Supplier">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 11.5px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                                            {{ strtoupper(substr($order->supplier?->nama_supplier ?? 'S', 0, 2)) }}
                                        </div>
                                        <div style="min-width: 0;">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 13.5px;">{{ $order->supplier?->nama_supplier ?? '-' }}</div>
                                            <div class="text-muted text-truncate" style="font-size: 11.5px;">
                                                Kode: <span class="fw-semibold text-secondary">{{ $order->supplier?->kode_supplier ?? '-' }}</span>
                                                @if(!empty($order->supplier?->asal_kota))
                                                    • {{ $order->supplier->asal_kota }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Gudang & Armada">
                                    <div class="fw-semibold text-dark" style="font-size: 13px;">{{ $order->branch?->nama_cabang ?? 'Gudang Utama' }}</div>
                                    <div class="text-muted" style="font-size: 11.5px;">
                                        @if($order->nama_supir || $order->plat_nomor)
                                            {{ $order->nama_supir ?: '-' }} {{ $order->plat_nomor ? "({$order->plat_nomor})" : '' }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end" data-label="Muatan Kain">
                                    <div class="fw-bold text-dark" style="font-size: 13.5px;">Rp {{ number_format($order->total_nominal, 0, ',', '.') }}</div>
                                    <div class="text-muted" style="font-size: 11.5px;">{{ number_format($order->total_rol) }} Rol ({{ number_format($order->total_meter, 1) }}m)</div>
                                </td>
                                <td class="text-center" data-label="Status">
                                    @if($order->status === 'menunggu_approval')
                                        <span class="status-pill status-warning"><span class="dot"></span> Menunggu ACC</span>
                                    @elseif($order->status === 'disetujui_admin' || $order->status === 'dikirim')
                                        <span class="status-pill status-info"><span class="dot"></span> Disetujui Admin</span>
                                    @elseif($order->status === 'dalam_perjalanan')
                                        <span class="status-pill status-info" style="background: #e0f2fe; color: #0284c7; border-color: #bae6fd;"><span class="dot" style="background: #0284c7;"></span> Dalam Perjalanan</span>
                                    @elseif($order->status === 'diterima')
                                        <span class="status-pill status-success"><span class="dot"></span> Diterima</span>
                                    @else
                                        <span class="status-pill status-danger"><span class="dot"></span> Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4" data-label="Aksi">
                                    <!-- Action 3-Dots Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn-action-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                            <i class="bi bi-three-dots-vertical fs-6"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-1" style="border-radius: 12px; font-size: 13px; min-width: 190px;">
                                            @if($order->status === 'menunggu_approval')
                                                <li>
                                                    <a href="{{ route(request()->routeIs('admin*') ? 'admin.delivery-orders.show' : 'gudang.delivery-orders.show', $order->id) }}" class="dropdown-item py-2 px-3 fw-bold text-warning-emphasis">
                                                        <i class="bi bi-shield-check me-2"></i> Tinjau & ACC Admin
                                                    </a>
                                                </li>
                                            @elseif($order->status === 'disetujui_admin' || $order->status === 'dikirim')
                                                <li>
                                                    <a href="{{ route(request()->routeIs('admin*') ? 'admin.delivery-orders.show' : 'gudang.delivery-orders.show', $order->id) }}" class="dropdown-item py-2 px-3 text-muted">
                                                        <i class="bi bi-clock me-2"></i> Menunggu Supplier Kirim
                                                    </a>
                                                </li>
                                            @elseif($order->status === 'dalam_perjalanan')
                                                <li>
                                                    <a href="{{ route(request()->routeIs('admin*') ? 'admin.delivery-orders.show' : 'gudang.delivery-orders.show', $order->id) }}" class="dropdown-item py-2 px-3 fw-bold text-success">
                                                        <i class="bi bi-box-seam me-2"></i> Cek & Terima Gudang
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a href="{{ route(request()->routeIs('admin*') ? 'admin.delivery-orders.show' : 'gudang.delivery-orders.show', $order->id) }}" class="dropdown-item py-2 px-3">
                                                    <i class="bi bi-eye me-2 text-primary"></i> Lihat Detail Surat Jalan
                                                </a>
                                            </li>
                                            <li>
                                                <button type="button" onclick="directPrintSuratJalan('{{ route('supplier.delivery-orders.print', $order->id) }}')" class="dropdown-item py-2 px-3">
                                                    <i class="bi bi-printer me-2 text-secondary"></i> Cetak Surat Jalan
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination Bar -->
            <div class="p-3 border-top bg-white d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="text-muted small ms-2">
                    Menampilkan <strong>{{ $deliveryOrders->firstItem() }} - {{ $deliveryOrders->lastItem() }}</strong> dari <strong>{{ $deliveryOrders->total() }}</strong> Surat Jalan
                </div>
                <div>
                    {{ $deliveryOrders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                <h6 class="fw-bold text-dark mb-1">Tidak ada data surat jalan online ditemukan</h6>
                <p class="small text-muted mb-0">Surat jalan yang dikirim oleh rekanan supplier akan otomatis muncul di sini.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function directPrintSuratJalan(printUrl) {
        let printFrame = document.getElementById('directPrintIframe');
        if (!printFrame) {
            printFrame = document.createElement('iframe');
            printFrame.id = 'directPrintIframe';
            printFrame.style.position = 'fixed';
            printFrame.style.right = '0';
            printFrame.style.bottom = '0';
            printFrame.style.width = '0px';
            printFrame.style.height = '0px';
            printFrame.style.border = 'none';
            printFrame.style.visibility = 'hidden';
            document.body.appendChild(printFrame);
        }
        printFrame.onload = function() {
            try {
                printFrame.contentWindow.focus();
                printFrame.contentWindow.print();
            } catch (e) {
                console.error('Print iframe error:', e);
            }
        };
        printFrame.src = printUrl;
    }

    function handleLiveSearch(input) {
        const query = input.value.trim().toLowerCase();
        const rows = document.querySelectorAll('.table-row-item');
        const resetBtn = document.getElementById('resetSearchBtn');
        let visibleCount = 0;

        if (query.length > 0) {
            if (resetBtn) resetBtn.classList.remove('d-none');
        } else {
            if (resetBtn) resetBtn.classList.add('d-none');
        }

        // Instant Zero-Reload DOM Filtering
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (!query || text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Toggle Empty Search Result Indicator
        let noMatchRow = document.getElementById('noMatchRow');
        if (visibleCount === 0 && rows.length > 0 && query.length > 0) {
            if (!noMatchRow) {
                const tbody = document.getElementById('tableBody');
                noMatchRow = document.createElement('tr');
                noMatchRow.id = 'noMatchRow';
                noMatchRow.innerHTML = `
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-search fs-3 d-block mb-2 text-secondary"></i>
                        <span class="fw-semibold text-dark">Tidak ada surat jalan yang cocok dengan "${input.value}"</span>
                    </td>
                `;
                tbody.appendChild(noMatchRow);
            } else {
                noMatchRow.style.display = '';
            }
        } else if (noMatchRow) {
            noMatchRow.style.display = 'none';
        }
    }

    function clearLiveSearch() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.value = '';
            handleLiveSearch(searchInput);
        }
    }

    // Run once on DOM ready if there is pre-filled query
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        if (searchInput && searchInput.value) {
            handleLiveSearch(searchInput);
        }
    });
</script>
@endpush
