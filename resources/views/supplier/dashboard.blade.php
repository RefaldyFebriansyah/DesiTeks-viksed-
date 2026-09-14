@extends('layouts.supplier')

@section('title', 'Beranda Supplier')

@push('styles')
<style>
    /* Hero Section - Full Bleed Edge to Edge */
    .sup-hero {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.82) 0%, rgba(0, 0, 0, 0.65) 100%), url('{{ asset('images/fabric_hero.jpg') }}') center center / cover no-repeat;
        border-radius: 0;
        width: 100vw;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        margin-top: -45px;
        margin-bottom: 90px;
        min-height: calc(100vh - 60px);
        padding: 90px 0;
        color: #fff;
        overflow: hidden;
        box-shadow: 0 12px 36px rgba(15, 23, 42, 0.3);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .sup-hero .btn-hero-primary {
        display: inline-flex; align-items: center; gap: 8px;
        background: #3b82f6; color: #fff; border: none;
        padding: 13px 28px; border-radius: 12px; font-size: 15px; font-weight: 700;
        text-decoration: none; transition: all 0.25s ease;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
    }
    .sup-hero .btn-hero-primary:hover { background: #2563eb; color: #fff; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(59, 130, 246, 0.5); }
    .sup-hero .btn-hero-outline {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.95); border: 1px solid rgba(255,255,255,0.35);
        padding: 13px 26px; border-radius: 12px; font-size: 15px; font-weight: 600;
        text-decoration: none; transition: all 0.25s ease;
        backdrop-filter: blur(8px);
    }
    .sup-hero .btn-hero-outline:hover { background: rgba(255,255,255,0.18); color: #fff; border-color: rgba(255,255,255,0.6); transform: translateY(-2px); }
    .sup-hero h1 { font-size: 40px; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 14px; text-shadow: 0 4px 12px rgba(0,0,0,0.4); line-height: 1.25; }
    .sup-hero p  { color: rgba(255,255,255,0.88); font-size: 16px; line-height: 1.7; margin-bottom: 32px; max-width: 700px; text-shadow: 0 2px 8px rgba(0,0,0,0.4); }

    /* Stat Cards */
    .stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 22px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        border-color: #3b82f6;
        box-shadow: 0 14px 30px -4px rgba(15, 23, 42, 0.08), 0 4px 12px rgba(59, 130, 246, 0.12);
    }
    .stat-icon {
        width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .stat-card:hover .stat-icon {
        transform: scale(1.12) rotate(4deg);
    }
    .stat-label { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 8px; }
    .stat-value { font-size: 28px; font-weight: 800; color: #111827; line-height: 1; margin-bottom: 4px; }
    .stat-sub   { font-size: 12px; color: #9ca3af; }

    /* Content Cards */
    .dash-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
    }
    .dash-card:hover {
        transform: translateY(-4px);
        border-color: #cbd5e1;
        box-shadow: 0 14px 30px -4px rgba(15, 23, 42, 0.08);
    }
    .dash-card-header {
        padding: 18px 20px; border-bottom: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between;
    }
    .dash-card-header h6 { font-size: 15px; font-weight: 700; color: #111827; margin: 0; }
    .dash-card-header .sub { font-size: 12px; color: #9ca3af; margin: 2px 0 0; }

    /* Dashboard Table */
    .dash-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .dash-table thead th {
        padding: 10px 16px; font-size: 11px; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.04em;
        background: #f9fafb; border-bottom: 1px solid #f3f4f6;
        white-space: nowrap;
    }
    .dash-table tbody td {
        padding: 12px 16px; border-bottom: 1px solid #f9fafb;
        color: #374151; vertical-align: middle;
    }
    .dash-table tbody tr:hover { background: #f8fafc; }
    .dash-table tbody tr:last-child td { border-bottom: none; }

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

    /* Sidebar Identity */
    .identity-avatar {
        width: 44px; height: 44px; border-radius: 10px; font-size: 16px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        color: #fff; background: #1e3a8a; flex-shrink: 0;
    }
    .identity-info-row { padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
    .identity-info-row:last-child { border-bottom: none; padding-bottom: 0; }
    .identity-info-label { font-size: 10.5px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 2px; }
    .identity-info-value { font-size: 13.5px; color: #374151; font-weight: 500; word-break: break-word; }

    /* Feature cards */
    .feat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
        height: 100%;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
    }
    .feat-card:hover {
        transform: translateY(-5px);
        border-color: #3b82f6;
        box-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.1), 0 4px 12px rgba(59, 130, 246, 0.12);
    }
    .feat-icon {
        width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .feat-card:hover .feat-icon {
        transform: scale(1.12) rotate(-4deg);
    }

    /* SOP Section */
    .sop-section { background: transparent; border: none; padding: 0; }
    .sop-step {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
        height: 100%;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
    }
    .sop-step:hover {
        transform: translateY(-5px);
        border-color: #3b82f6;
        box-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.1), 0 4px 12px rgba(59, 130, 246, 0.12);
    }
    .sop-num {
        width: 34px; height: 34px; border-radius: 10px; font-size: 14px; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
        background: #eff6ff; color: #2563eb; margin-bottom: 14px;
        transition: transform 0.3s ease;
    }
    .sop-step:hover .sop-num {
        transform: scale(1.15);
    }

    /* Section Header */
    .section-header { margin-bottom: 32px; margin-top: 16px; }
    .section-header .label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #3b82f6; margin-bottom: 6px; }
    .section-header h4 { font-size: 22px; font-weight: 800; color: #111827; letter-spacing: -0.02em; margin-bottom: 6px; }
    .section-header p { font-size: 14px; color: #6b7280; margin: 0; }

    /* Comprehensive Mobile Responsive Media Queries */
    @media (max-width: 991.98px) {
        .sup-hero {
            padding: 60px 0;
            min-height: auto;
            margin-top: -30px;
            margin-bottom: 50px;
        }
        .sup-hero h1 { font-size: 30px; }
        .sup-hero p { font-size: 15px; margin-bottom: 24px; }
    }

    @media (max-width: 576px) {
        .sup-hero {
            padding: 40px 0;
            margin-top: -24px;
            margin-bottom: 36px;
        }
        .sup-hero h1 { font-size: 22px; line-height: 1.3; }
        .sup-hero p { font-size: 13px; line-height: 1.6; margin-bottom: 20px; }
        .sup-hero .btn-hero-primary,
        .sup-hero .btn-hero-outline {
            width: 100%;
            justify-content: center;
            padding: 11px 18px;
            font-size: 13.5px;
        }
        .stat-card { padding: 14px; }
        .stat-value { font-size: 20px; }
        .stat-label { font-size: 10.5px; }
        .stat-icon { width: 34px; height: 34px; font-size: 15px; }
        .dash-card-header { flex-direction: column; align-items: flex-start; gap: 8px; }
        .dash-table { font-size: 12px; }
        .dash-table thead th, .dash-table tbody td { padding: 8px 10px; }
        .section-header { margin-bottom: 20px; margin-top: 10px; }
        .section-header h4 { font-size: 18px; }
        .section-header p { font-size: 13px; }
        .sop-step { padding: 16px; }
    }
</style>
@endpush

@section('content')
<div>
    {{-- Hero --}}
    <div class="sup-hero">
        <div class="container-xl position-relative" style="z-index: 2;">
            @if($user)
                <h1>Selamat Datang, {{ $supplier?->nama_supplier ?? $user->name }}!</h1>
                <p>Kelola pembuatan surat jalan digital, pantau status verifikasi barang, dan riwayat pengiriman kain Anda ke gudang {{ $storeName }}.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('supplier.delivery-orders.create') }}" class="btn-hero-primary">
                        <i class="bi bi-plus-lg"></i> Buat Surat Jalan Baru
                    </a>
                    <a href="{{ route('supplier.delivery-orders.index') }}" class="btn-hero-outline">
                        <i class="bi bi-truck"></i> Daftar Pengiriman
                    </a>
                </div>
            @else
                <h1>Penerimaan Pasokan Kain & Surat Jalan Online</h1>
                <p>Portal resmi penerimaan pasokan kain dan penerbitan surat jalan digital mitra supplier {{ $storeName }}.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="btn-hero-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk Akun Supplier
                    </a>
                    <a href="{{ route('register') }}" class="btn-hero-outline">
                        <i class="bi bi-person-plus-fill"></i> Daftar Mitra Baru
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Announcement Banner from Admin --}}
    @if(!empty($pengumumanSupplier))
        <div class="alert bg-white border shadow-sm rounded-3 p-3.5 mb-4 d-flex align-items-start gap-3">
            <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                <i class="bi bi-info-circle-fill fs-6"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">Catatan Manajemen {{ $storeName }}:</div>
                <div class="text-secondary small mb-0" style="line-height: 1.5; font-size: 13px;">{{ $pengumumanSupplier }}</div>
            </div>
        </div>
    @endif

    {{-- Stat Metrics (Only for Logged-In Suppliers) vs Guest Showcase --}}
    @if($user)
    <div class="row g-3 mb-5 pb-2" id="stat-section">
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div class="stat-label">Total Surat Jalan</div>
                    <div class="stat-icon" style="background: #eff6ff; color: #2563eb;"><i class="bi bi-file-earmark-text"></i></div>
                </div>
                <div class="stat-value">{{ number_format($totalSuratJalan) }}</div>
                <div class="stat-sub">Dokumen pengiriman Anda</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div class="stat-label">Sedang Dikirim</div>
                    <div class="stat-icon" style="background: #fef3c7; color: #d97706;"><i class="bi bi-truck"></i></div>
                </div>
                <div class="stat-value" style="color: #d97706;">{{ number_format($sedangDikirim) }}</div>
                <div class="stat-sub">Dalam perjalanan / cek</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div class="stat-label">Diterima Gudang</div>
                    <div class="stat-icon" style="background: #d1fae5; color: #059669;"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="stat-value" style="color: #059669;">{{ number_format($diterima) }}</div>
                <div class="stat-sub">Masuk ke stok toko</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div class="stat-label">Volume Diterima</div>
                    <div class="stat-icon" style="background: #e0e7ff; color: #4f46e5;"><i class="bi bi-layers-fill"></i></div>
                </div>
                <div class="stat-value">{{ number_format($totalRol) }} <span style="font-size: 14px; font-weight: 500; color: #9ca3af;">rol</span></div>
                <div class="stat-sub">{{ number_format($totalMeter, 1) }} meter kain</div>
            </div>
        </div>
    </div>
    @else
    {{-- Guest Feature Showcase (Before Login) --}}
    <div class="row g-3 mb-5 pb-2" id="stat-section">
        <div class="col-md-4">
            <div class="feat-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="feat-icon" style="background: #eff6ff; color: #2563eb;"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 14.5px;">Surat Jalan Digital Instant</div>
                        <div class="text-muted" style="font-size: 12px;">Penerbitan Elektronik</div>
                    </div>
                </div>
                <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.6;">
                    Terbitkan surat jalan kain secara online tanpa perlu cetak manual. Data pengiriman langsung tercatat dan dapat diverifikasi oleh admin toko.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feat-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="feat-icon" style="background: #d1fae5; color: #059669;"><i class="bi bi-truck-flatbed"></i></div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 14.5px;">Tracking Status Real-time</div>
                        <div class="text-muted" style="font-size: 12px;">Transparansi Logistik</div>
                    </div>
                </div>
                <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.6;">
                    Lacak status pengiriman armada Anda mulai dari ACC Admin, proses verifikasi fisik di gudang, hingga status tanda terima akhir.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feat-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="feat-icon" style="background: #fef3c7; color: #d97706;"><i class="bi bi-box-seam-fill"></i></div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 14.5px;">Sinkronisasi Stok Gudang</div>
                        <div class="text-muted" style="font-size: 12px;">Pencatatan Otomatis</div>
                    </div>
                </div>
                <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.6;">
                    Jumlah rol dan meteran kain yang diterima oleh tim gudang otomatis terintegrasi langsung dengan sistem manajemen stok DesiTeks.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Section --}}
    @if($user)
    <div class="row g-3 mb-5 pb-2">
        {{-- Pengiriman Terkini --}}
        <div class="col-lg-8">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-journal-text text-secondary fs-5"></i>
                        <h6 class="m-0 fw-bold text-dark">Pengiriman Terkini</h6>
                    </div>
                    <a href="{{ route('supplier.delivery-orders.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-1 fw-semibold" style="border-radius: 8px; font-size: 13px; color: #374151; border-color: #cbd5e1;">
                        Lihat Semua
                    </a>
                </div>
                @if($recentDeliveries->count() > 0)
                    <div style="overflow-x: auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th style="padding-left: 20px;">NO. SURAT JALAN</th>
                                    <th>GUDANG TUJUAN</th>
                                    <th>TOTAL</th>
                                    <th style="padding-right: 20px; text-align: center;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentDeliveries as $order)
                                <tr>
                                    <td style="padding-left: 20px;">
                                        <a href="{{ route('supplier.delivery-orders.show', $order->id) }}" class="fw-bold text-dark text-decoration-none d-block" style="font-size: 13.5px;">
                                            {{ $order->nomor_surat_jalan }}
                                        </a>
                                        <div style="font-size: 11px; color: #9ca3af;">{{ $order->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <span class="text-secondary" style="font-weight: 500;">{{ $order->branch?->nama_cabang ?? 'Cabang Utama (Pusat)' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 13.5px;">Rp {{ number_format($order->total_nominal, 0, ',', '.') }}</div>
                                        @if($order->total_rol > 0)
                                            <div style="font-size: 11px; color: #9ca3af;">{{ $order->total_rol }} Rol ({{ number_format($order->total_meter, 1) }}m)</div>
                                        @endif
                                    </td>
                                    <td style="padding-right: 20px; text-align: center;">
                                        @if($order->status === 'menunggu_approval')
                                            <span class="status-pill status-warning"><span class="dot"></span> Menunggu ACC</span>
                                        @elseif($order->status === 'disetujui_admin' || $order->status === 'dikirim')
                                            <span class="status-pill status-info"><span class="dot"></span> Sedang Dikirim</span>
                                        @elseif($order->status === 'diterima')
                                            <span class="status-pill status-success"><span class="dot"></span> Diterima</span>
                                        @else
                                            <span class="status-pill status-danger"><span class="dot"></span> Ditolak</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 36px;"></i>
                        <p class="fw-semibold text-dark mt-2 mb-2" style="font-size: 14px;">Belum ada surat jalan</p>
                        <a href="{{ route('supplier.delivery-orders.create') }}" class="btn btn-sm btn-primary rounded-2 px-4 fw-semibold" style="font-size: 12.5px;">
                            <i class="bi bi-plus-lg me-1"></i> Buat Surat Jalan Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar Identitas --}}
        <div class="col-lg-4">
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h6>Identitas Rekanan</h6>
                    <a href="{{ route('supplier.profile.index') }}" class="text-primary text-decoration-none fw-semibold" style="font-size: 12px;">
                        Profil <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div style="padding: 16px 20px;">
                    {{-- Company Info --}}
                    <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded-3" style="background: #f9fafb; border: 1px solid #f3f4f6;">
                        <div class="identity-avatar">
                            {{ strtoupper(substr($supplier?->nama_supplier ?? $user->name, 0, 2)) }}
                        </div>
                        <div style="min-width: 0;">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 15px;">{{ $supplier?->nama_supplier ?? '-' }}</div>
                            <span style="font-size: 11px; font-weight: 600; color: #3b82f6;">{{ $supplier?->kode_supplier ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Detail Rows --}}
                    <div class="identity-info-row">
                        <div class="identity-info-label">PIC / Penanggung Jawab</div>
                        <div class="identity-info-value fw-semibold">{{ $user->name }}</div>
                    </div>
                    <div class="identity-info-row">
                        <div class="identity-info-label">Email</div>
                        <div class="identity-info-value">{{ $supplier?->email ?? $user->email }}</div>
                    </div>
                    <div class="identity-info-row">
                        <div class="identity-info-label">Telepon</div>
                        <div class="identity-info-value">{{ $supplier?->no_telepon ?? '-' }}</div>
                    </div>
                    <div class="identity-info-row">
                        <div class="identity-info-label">Alamat</div>
                        <div class="identity-info-value">{{ $supplier?->alamat ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Quick Info Card --}}
            <div class="dash-card">
                <div style="padding: 18px 20px;">
                    <div class="d-flex gap-3">
                        <div class="stat-icon flex-shrink-0" style="background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-1" style="font-size: 14px;">Pengiriman Aman & Tercatat</div>
                            <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.55;">
                                Dokumen surat jalan online diproses cepat oleh tim gudang saat armada pengirim tiba di lokasi bongkar muatan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        {{-- Guest Callout --}}
        <div class="dash-card p-5 mb-5 text-center">
            <div class="stat-icon mx-auto mb-3" style="width: 56px; height: 56px; font-size: 24px; background: #eff6ff; color: #2563eb;">
                <i class="bi bi-box-arrow-in-right"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Ingin Menerbitkan Surat Jalan?</h5>
            <p class="text-muted mb-3" style="font-size: 14px; max-width: 480px; margin: 0 auto;">
                Masuk menggunakan akun mitra supplier Anda atau daftar perusahaan baru untuk mengakses formulir surat jalan digital.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-primary fw-bold px-4 py-2 rounded-2" style="font-size: 13.5px;">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Akun
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-secondary fw-semibold px-4 py-2 rounded-2" style="font-size: 13.5px;">
                    <i class="bi bi-person-plus-fill me-1"></i> Daftar Mitra Baru
                </a>
            </div>
        </div>
    @endif

    {{-- Kategori Kain --}}
    <div class="mb-5 pb-3">
        <div class="section-header text-center">
            <div class="label">Bahan Baku Tekstil</div>
            <h4>Kategori Kain yang Diterima</h4>
            <p>Kami menerima pasokan kain berkualitas dari pabrik tekstil dan supplier rekanan.</p>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="feat-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="feat-icon" style="background: #eff6ff; color: #2563eb;"><i class="bi bi-layers-fill"></i></div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">Katun & Rayon</div>
                            <div class="text-muted" style="font-size: 12px;">Bahan Pakaian Harian</div>
                        </div>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.6;">
                        Katun Combed, Rayon Twill, Rayon Viscose, Poplin, Voal, dan varian katun bermutu tinggi dalam satuan rol dan meter standar pabrik.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feat-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="feat-icon" style="background: #d1fae5; color: #059669;"><i class="bi bi-bounding-box-circles"></i></div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">Linen & Woven</div>
                            <div class="text-muted" style="font-size: 12px;">Kain Serat & Tekstur</div>
                        </div>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.6;">
                        Linen Pure, Linen Slub, Canvas, Twill Drill, Oxford, dan aneka bahan tenun untuk seragam, busana, serta kebutuhan industri kreatif.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feat-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="feat-icon" style="background: #fef3c7; color: #d97706;"><i class="bi bi-gem"></i></div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">Sutra & Bahan Mewah</div>
                            <div class="text-muted" style="font-size: 12px;">Kain Fashion & Pesta</div>
                        </div>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.6;">
                        Sutra Satin, Organza, Crepe, Ceruty, Shiffon, Tulle, dan aneka kain gaun berkualitas premium untuk perancang busana.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- SOP Alur Kerja --}}
    <div class="sop-section mb-5 pb-4">
        <div class="section-header text-center">
            <div class="label">SOP Penerimaan</div>
            <h4>Alur Pengiriman Kain ke Gudang</h4>
            <p>Proses terstruktur dari surat jalan online hingga penambahan stok di gudang DesiTeks.</p>
        </div>

        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="sop-step">
                    <div class="sop-num">1</div>
                    <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">Isi Surat Jalan Online</div>
                    <p class="text-muted mb-0" style="font-size: 12px; line-height: 1.55;">
                        Input no. surat jalan, armada/supir, serta rincian rol & meteran kain.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="sop-step">
                    <div class="sop-num" style="background: #fef3c7; color: #d97706;">2</div>
                    <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">ACC Admin Toko</div>
                    <p class="text-muted mb-0" style="font-size: 12px; line-height: 1.55;">
                        Admin toko memeriksa & menyetujui (ACC) pengiriman online sebelum fisik tiba.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="sop-step">
                    <div class="sop-num" style="background: #e0e7ff; color: #4f46e5;">3</div>
                    <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">Pemeriksaan Fisik Gudang</div>
                    <p class="text-muted mb-0" style="font-size: 12px; line-height: 1.55;">
                        Staf gudang mencocokkan jumlah rol dan meteran fisik kain dengan surat jalan.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="sop-step">
                    <div class="sop-num" style="background: #d1fae5; color: #059669;">4</div>
                    <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">Stok Masuk Otomatis</div>
                    <p class="text-muted mb-0" style="font-size: 12px; line-height: 1.55;">
                        Saat diklik <strong>Terima</strong>, stok kain otomatis bertambah di inventaris toko.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
