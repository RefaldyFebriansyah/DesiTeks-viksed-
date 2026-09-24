@extends('layouts.supplier')

@section('title', 'Surat Jalan Online')

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
    <!-- Header Title & Subtitle -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.02em;">Surat Jalan Online</h3>
            <div class="text-secondary small">Daftar seluruh pengiriman kain dan status verifikasi penerimaan di Gudang MitraSeratBuana</div>
        </div>
        <a href="{{ route('supplier.delivery-orders.create') }}" class="btn btn-primary px-4 py-2.5 rounded-pill fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="font-size: 13.5px;">
            <i class="bi bi-plus-lg"></i>
            <span>Buat Surat Jalan Baru</span>
        </a>
    </div>

    <!-- Main Card Container -->
    <div class="dt-card-table">
        <!-- Top Search Bar & Filters -->
        <div class="p-3 border-bottom bg-white">
            <form action="{{ route('supplier.delivery-orders.index') }}" method="GET" id="searchForm" onsubmit="event.preventDefault(); return false;">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5 col-lg-4">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" name="q" id="searchInput" class="form-control search-filter-input ps-5" placeholder="Cari No. Surat Jalan / Supir / Plat..." value="{{ $search }}" autocomplete="off" oninput="handleLiveSearch(this)">
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <select name="status" class="form-select search-filter-input" onchange="window.location.href='{{ route('supplier.delivery-orders.index') }}?status=' + this.value">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status ({{ $counts['all'] }})</option>
                            <option value="menunggu_approval" {{ $status === 'menunggu_approval' ? 'selected' : '' }}>Menunggu ACC Admin ({{ $counts['menunggu_approval'] }})</option>
                            <option value="disetujui_admin" {{ $status === 'disetujui_admin' ? 'selected' : '' }}>Disetujui Admin Toko</option>
                            <option value="dalam_perjalanan" {{ $status === 'dalam_perjalanan' ? 'selected' : '' }}>Dalam Perjalanan</option>
                            <option value="diterima" {{ $status === 'diterima' ? 'selected' : '' }}>Diterima Gudang ({{ $counts['diterima'] }})</option>
                            <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak ({{ $counts['ditolak'] }})</option>
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
                            <th>Gudang Tujuan</th>
                            <th>Supir & Armada</th>
                            <th class="text-end">Total Muatan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center pe-4" style="width: 50px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach($deliveryOrders as $order)
                            <tr class="table-row-item" data-id="{{ $order->id }}" data-status="{{ $order->status }}">
                                <td class="ps-4" data-label="No. Surat Jalan">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 bg-light d-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 32px; height: 32px; font-size: 14px;">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('supplier.delivery-orders.show', $order->id) }}" class="fw-bold text-dark text-decoration-none d-block" style="font-size: 13.5px;">
                                                {{ $order->nomor_surat_jalan }}
                                            </a>
                                            <div class="text-muted" style="font-size: 11px;">
                                                Tgl: {{ $order->tanggal_kirim ? $order->tanggal_kirim->format('d/m/Y') : $order->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Gudang Tujuan">
                                    <div class="fw-bold text-dark" style="font-size: 13.5px;">{{ $order->branch?->nama_cabang ?? 'Gudang Utama' }}</div>
                                    <div class="text-muted" style="font-size: 11.5px;">{{ $order->branch?->kota ?? 'Bandung' }}</div>
                                </td>
                                <td data-label="Supir & Armada">
                                    @if($order->nama_supir || $order->plat_nomor)
                                        <div class="fw-semibold text-dark" style="font-size: 13px;">{{ $order->nama_supir ?: '-' }}</div>
                                        <div class="text-muted" style="font-size: 11.5px;">{{ $order->plat_nomor ?: '-' }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end" data-label="Total Muatan">
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
                                        <span class="status-pill status-success"><span class="dot"></span> Diterima Gudang</span>
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
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-1" style="border-radius: 12px; font-size: 13px; min-width: 180px;">
                                            <li>
                                                <a href="{{ route('supplier.delivery-orders.show', $order->id) }}" class="dropdown-item py-2 px-3">
                                                    <i class="bi bi-eye me-2 text-primary"></i> Lihat Detail
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
                <h6 class="fw-bold text-dark mb-1">Tidak ada data surat jalan ditemukan</h6>
                <p class="small text-muted mb-3">Mulai buat surat jalan baru untuk pengiriman kain ke gudang MitraSeratBuana.</p>
                <a href="{{ route('supplier.delivery-orders.create') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold small">
                    <i class="bi bi-plus-lg me-1"></i> Buat Surat Jalan Baru
                </a>
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

    // Real-time polling for list view (Zero-Reload / Non-Kedip)
    (function() {
        let checkUrl = "{{ route('supplier.delivery-orders.check-all-status') }}";

        function getVisibleIds() {
            let rows = document.querySelectorAll('.table-row-item[data-id]');
            let ids = [];
            rows.forEach(r => ids.push(r.dataset.id));
            return ids;
        }

        function showStatusToast(message) {
            let old = document.getElementById('do-status-toast');
            if (old) old.remove();

            let toast = document.createElement('div');
            toast.id = 'do-status-toast';
            toast.style.cssText = 'position:fixed; top:20px; right:20px; background:#0f172a; color:#fff; padding:14px 20px; border-radius:12px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.3); z-index:99999; font-weight:600; font-size:13.5px; display:flex; align-items:center; gap:10px; border:1px solid #334155; transition: opacity 0.3s ease;';
            toast.innerHTML = '<span style="font-size:18px;">🚀</span> <span>' + message + '</span>';
            document.body.appendChild(toast);

            setTimeout(function() {
                if (toast) {
                    toast.style.opacity = '0';
                    setTimeout(function() { toast.remove(); }, 300);
                }
            }, 4000);
        }

        setInterval(function() {
            let ids = getVisibleIds();
            if (ids.length === 0) return;

            let queryParams = ids.map(id => 'ids[]=' + encodeURIComponent(id)).join('&');
            fetch(checkUrl + '?' + queryParams, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(statuses => {
                let changed = false;
                let rows = document.querySelectorAll('.table-row-item[data-id]');
                rows.forEach(r => {
                    let id = r.dataset.id;
                    let current = r.dataset.status;
                    if (statuses[id] && statuses[id].status !== current) {
                        changed = true;
                    }
                });

                if (changed) {
                    showStatusToast('Status surat jalan diperbarui!');
                    fetch(window.location.href, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newWrapper = doc.querySelector('.table-responsive-wrapper');
                        const curWrapper = document.querySelector('.table-responsive-wrapper');
                        if (newWrapper && curWrapper) {
                            curWrapper.innerHTML = newWrapper.innerHTML;
                        }
                    })
                    .catch(err => {});
                }
            })
            .catch(err => {});
        }, 4000);
    })();
</script>
@endpush
