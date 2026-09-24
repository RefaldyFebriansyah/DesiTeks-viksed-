@php
    $isKasir = auth()->check() && auth()->user()->role === 'kasir';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $storeName) — {{ $storeName }}</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/logo_icon_light.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_icon_light.png') }}">

    <!-- PWA Settings -->
    <meta name="theme-color" content="#0f2744">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ $storeName }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_icon_light.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/desiteks.css') }}">
    <style>
        /* Bell Notification Button Styling (Anti-Gepeng & Sleek Circular SaaS Style) */
        .dt-bell-btn {
            width: 36px;
            height: 36px;
            padding: 0 !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            position: relative;
            cursor: pointer;
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            color: #475569 !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            flex-shrink: 0;
            outline: none !important;
        }
        .dt-bell-btn:hover {
            background: #f8fafc !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(15,23,42,0.06) !important;
        }
        .dt-bell-btn:active {
            transform: translateY(0);
        }
        .dt-bell-icon {
            display: block;
            flex-shrink: 0;
            transition: transform 0.25s ease;
        }
        .dt-bell-btn:hover .dt-bell-icon {
            transform: rotate(12deg) scale(1.05);
        }
        #notifBadge {
            top: -3px !important;
            right: -3px !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            min-width: 18px !important;
            height: 18px !important;
            padding: 0 4px !important;
            border-radius: 9999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 2px solid #ffffff !important;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.45) !important;
            line-height: 1 !important;
        }

        /* Dark / Kasir Mode Overrides */
        body.role-kasir .dt-bell-btn {
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            background: rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
            box-shadow: none !important;
        }
        body.role-kasir .dt-bell-btn:hover {
            background: rgba(255, 255, 255, 0.16) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.25) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
        }
        body.role-kasir #notifBadge {
            border-color: #0f172a !important;
        }

        /* Notification Centered Modal Animation & Polishing */
        #notifModal .modal-content {
            border-radius: 22px !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.2) !important;
            animation: notifModalPop 0.24s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes notifModalPop {
            from {
                opacity: 0;
                transform: scale(0.94) translateY(10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        #notifModal .btn-close:focus {
            box-shadow: none;
        }
        #notifModal .notif-card:hover {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }
        /* Pastikan modal selalu di atas backdrop dan dapat diklik */
        .modal {
            z-index: 1060 !important;
        }
        .modal-backdrop {
            z-index: 1050 !important;
        }
    </style>
    @stack('styles')
</head>
<body class="{{ auth()->check() ? 'role-' . auth()->user()->role : '' }}">
<div id="dt-page-progress"></div>
<div class="dt-wrapper">

    @php
        $isKasir = auth()->check() && auth()->user()->role === 'kasir';
    @endphp

    @if(!$isKasir)
        {{-- SIDEBAR BACKDROP FOR MOBILE --}}
        <div class="dt-sidebar-backdrop" id="sidebarOverlay" onclick="closeSidebarMobile()"></div>

        {{-- SIDEBAR --}}
        @include('partials.sidebar')
    @endif

    {{-- MAIN --}}
    <div class="dt-main">

        {{-- TOPBAR --}}
        <div class="dt-topbar px-3 px-md-4 py-2.5 {{ $isKasir ? '' : 'bg-white border-bottom' }} d-flex align-items-center justify-content-between">
            @if($isKasir)
                {{-- KASIR TOPBAR LEFT: BRAND + TOP NAV MENU --}}
                <div class="d-flex align-items-center gap-2 gap-lg-3 min-w-0">
                    <a href="{{ route('kasir.sales.pos') }}" class="d-flex align-items-center gap-2 text-decoration-none flex-shrink-0 me-1 me-lg-2">
                        <img src="{{ asset('images/logo_kainkita_transparent.png') }}" alt="KainKita Logo" style="height:32px; width:auto; object-fit:contain;">
                        <span class="fw-800 text-white" style="font-size:17px; letter-spacing:-0.3px;">{!! $formattedStoreNameLight !!}</span>
                        <span class="badge rounded-pill px-2 py-0.5 fw-bold ms-1" style="background: rgba(37, 99, 235, 0.25); color: #60a5fa; border: 1px solid rgba(96, 165, 250, 0.3); font-size: 10px; letter-spacing: 0.5px;">KASIR</span>
                    </a>

                    {{-- Horizontal Top Nav Menu (Desktop & Tablet) --}}
                    <nav class="d-none d-md-flex align-items-center gap-1 ms-1">
                        <a href="{{ route('kasir.sales.pos') }}" class="dt-top-nav-link {{ request()->routeIs('kasir.sales*') ? 'active' : '' }}">
                            <i class="bi bi-cart3"></i>
                            <span>POS Penjualan</span>
                        </a>
                        <a href="{{ route('kasir.transactions.index') }}" class="dt-top-nav-link {{ request()->routeIs('kasir.transactions*') ? 'active' : '' }}">
                            <i class="bi bi-wallet2"></i>
                            <span>Transaksi Hari Ini</span>
                        </a>
                        <a href="{{ route('kasir.income.index') }}" class="dt-top-nav-link {{ request()->routeIs('kasir.income*') ? 'active' : '' }}">
                            <i class="bi bi-cash-stack"></i>
                            <span>Rekap Pendapatan</span>
                        </a>
                    </nav>
                </div>
            @else
                <div class="d-flex align-items-center gap-2 min-w-0 flex-shrink-1">
                    <button class="btn btn-sm d-lg-none p-0 me-1" id="sidebarToggle" style="color:var(--dt-navy)" aria-label="Toggle Menu">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <a href="#" class="d-lg-none d-flex align-items-center gap-1.5 text-decoration-none me-1" onclick="openSidebarMobile(); return false;">
                            <img src="{{ asset('images/logo_icon_light.png') }}" alt="MitraSeratBuana Logo" style="height:26px; width:auto; object-fit:contain;">
                            <span class="fw-800 text-navy" style="font-size:16px; letter-spacing:-0.3px;">MitraSerat<span class="text-gold">Buana</span></span>
                        </a>
                        <span class="dt-topbar-title text-truncate fw-700 text-navy" style="font-size: 15px;">@yield('page-title', 'Dashboard')</span>
                    </div>
                </div>
            @endif

            <div class="dt-topbar-actions d-flex align-items-center gap-2 gap-md-3 flex-shrink-0">
                <!-- Live Clock Pill Widget -->
                <div class="d-none d-sm-flex align-items-center gap-2 border px-3 py-1.5 rounded-3 me-1" style="{{ $isKasir ? 'background: rgba(255, 255, 255, 0.07); border-color: rgba(255, 255, 255, 0.12) !important;' : 'background: #ffffff; border-color: #e2e8f0 !important; box-shadow: 0 1px 2px rgba(0,0,0,0.02);' }}">
                    <i class="bi bi-clock-fill" style="font-size: 13px; color: {{ $isKasir ? '#94a3b8' : '#64748b' }};"></i>
                    <span id="liveClock" class="fw-600" style="font-size: 13px; color: {{ $isKasir ? '#f1f5f9' : '#1e293b' }}; letter-spacing: 0.3px;">{{ now()->timezone('Asia/Jakarta')->format('H:i:s') }}</span>
                </div>

                <!-- Notification Bell Button (Modal Trigger) -->
                @php
                    $unreadNotifCount = \App\Models\AppNotification::unread()->count();
                @endphp
                <button type="button" class="btn dt-bell-btn position-relative" id="bellBtn" data-bs-toggle="modal" data-bs-target="#notifModal" onclick="fetchNotificationsAndClearBadge()" title="Notifikasi" aria-label="Notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="dt-bell-icon">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                    </svg>
                    <span class="position-absolute badge rounded-pill bg-danger text-white {{ $unreadNotifCount > 0 ? '' : 'd-none' }}" id="notifBadge" style="{{ $unreadNotifCount > 0 ? '' : 'display: none !important;' }}">
                        {{ $unreadNotifCount }}
                    </span>
                </button>

                <!-- Vertical Divider -->
                <div style="width: 1px; height: 24px; background-color: {{ $isKasir ? 'rgba(255, 255, 255, 0.15)' : '#e2e8f0' }}; margin: 0 4px;" class="d-none d-sm-block"></div>

                @php
                    $rawName   = auth()->user()->name ?? 'User';
                    $cleanName = preg_replace('/\s*\([^)]*\)/', '', $rawName);
                    
                    $words = explode(' ', trim($cleanName));
                    if (count($words) >= 2) {
                        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                    } else {
                        $initials = strtoupper(substr($cleanName, 0, 2));
                    }

                    $roleTitle = match(auth()->user()->role ?? '') {
                        'admin'  => 'System Admin',
                        'gudang' => 'Gudang Master',
                        'kasir'  => 'Kasir POS',
                        default  => ucfirst(auth()->user()->role ?? 'User'),
                    };
                @endphp

                <!-- Account Dropdown Widget -->
                <div class="dropdown">
                    <button type="button" class="btn p-0 border-0 d-flex align-items-center text-decoration-none shadow-none" data-bs-toggle="dropdown" aria-expanded="false" style="background: transparent; box-shadow: none !important;">
                        <div class="text-end d-none d-md-flex flex-column align-items-end flex-shrink-0 me-2" style="line-height: 1.25;">
                            <span class="fw-700 d-block" style="font-size: 13.5px; color: {{ $isKasir ? '#f8fafc' : '#0f172a' }} !important; white-space: nowrap;">{{ $cleanName }}</span>
                            <span class="d-block" style="font-size: 11px; font-weight: 500; color: {{ $isKasir ? '#94a3b8' : '#64748b' }} !important; white-space: nowrap;">{{ $roleTitle }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded-circle text-white flex-shrink-0 ms-1 me-2" style="width: 36px; height: 36px; background: #3b82f6; font-size: 13px; font-weight: 700; letter-spacing: 0.5px; box-shadow: 0 2px 5px rgba(59, 130, 246, 0.25);">
                            {{ $initials }}
                        </div>
                        <i class="bi bi-chevron-down flex-shrink-0" style="font-size: 11px; color: {{ $isKasir ? '#94a3b8' : '#64748b' }} !important;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 p-2" style="border-radius: 14px; min-width: 250px; font-size: 13px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15) !important;">
                        <li class="px-3 py-2 mb-2 rounded-3" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle text-white flex-shrink-0" style="width: 38px; height: 38px; background: #3b82f6; font-size: 13px; font-weight: 700;">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0 flex-grow-1">
                                    <div class="fw-700 text-navy text-truncate" style="font-size: 13.5px; line-height: 1.25;">{{ $cleanName }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 11.5px; margin-top: 2px;">{{ auth()->user()->email ?? (auth()->user()->username . '@mitraseratbuana.com') }}</div>
                                </div>
                            </div>
                            <div class="mt-2 pt-2 d-flex align-items-center justify-content-between" style="border-top: 1px solid #e2e8f0;">
                                <span class="text-muted" style="font-size: 11px; font-weight: 500;">Role Akses</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle" style="font-size: 10.5px; font-weight: 600;">{{ $roleTitle }}</span>
                            </div>
                        </li>
                        @if(auth()->user()->role === 'admin')
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="dropdown-item py-2 px-3 text-secondary fw-500 rounded-2 d-flex align-items-center gap-2">
                                <i class="bi bi-gear fs-6 text-primary"></i> Pengaturan Toko
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        @endif
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 px-3 text-danger fw-semibold rounded-2 d-flex align-items-center gap-2" style="font-size: 13px;">
                                    <i class="bi bi-box-arrow-right fs-6"></i>
                                    <span>Keluar dari Akun</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @if($isKasir)
            {{-- MOBILE SUBBAR NAVIGATION FOR KASIR (< 768px) --}}
            <div class="dt-kasir-mobile-nav d-flex d-md-none">
                <a href="{{ route('kasir.sales.pos') }}" class="dt-mobile-nav-link {{ request()->routeIs('kasir.sales*') ? 'active' : '' }}">
                    <i class="bi bi-cart3"></i>
                    <span>POS Penjualan</span>
                </a>
                <a href="{{ route('kasir.transactions.index') }}" class="dt-mobile-nav-link {{ request()->routeIs('kasir.transactions*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>Transaksi</span>
                </a>
                <a href="{{ route('kasir.income.index') }}" class="dt-mobile-nav-link {{ request()->routeIs('kasir.income*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i>
                    <span>Pendapatan</span>
                </a>
            </div>
        @endif

        {{-- PAGE CONTENT --}}
        <div class="dt-page">

            {{-- Toast Notification Container --}}
            <div class="dt-toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
                @if(session('success'))
                    <div class="dt-toast dt-toast-success show shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="dt-toast-content d-flex align-items-center gap-3">
                            <div class="dt-toast-icon bg-success-soft text-success rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; flex-shrink:0;">
                                <i class="bi bi-check-lg" style="font-size:18px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-700 text-navy" style="font-size:13px; line-height: 1.2;">Berhasil!</div>
                                <div class="text-muted" style="font-size:12px; margin-top: 2px;">{{ session('success') }}</div>
                            </div>
                            <button type="button" class="btn-close ms-2" onclick="closeToast(this)" aria-label="Close" style="background-size: 10px; opacity: 0.6; border: none; background-color: transparent;"></button>
                        </div>
                        <div class="dt-toast-progress bg-success"></div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="dt-toast dt-toast-danger show shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="dt-toast-content d-flex align-items-center gap-3">
                            <div class="dt-toast-icon bg-danger-soft text-danger rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; flex-shrink:0;">
                                <i class="bi bi-x-lg" style="font-size:18px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-700 text-navy" style="font-size:13px; line-height: 1.2;">Error!</div>
                                <div class="text-muted" style="font-size:12px; margin-top: 2px;">{{ session('error') }}</div>
                            </div>
                            <button type="button" class="btn-close ms-2" onclick="closeToast(this)" aria-label="Close" style="background-size: 10px; opacity: 0.6; border: none; background-color: transparent;"></button>
                        </div>
                        <div class="dt-toast-progress bg-danger"></div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="dt-toast dt-toast-danger show shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="dt-toast-content d-flex align-start gap-3">
                            <div class="dt-toast-icon bg-danger-soft text-danger rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; flex-shrink:0;">
                                <i class="bi bi-exclamation" style="font-size:20px; font-weight: bold;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-700 text-navy mb-1" style="font-size:13px; line-height: 1.2;">Peringatan!</div>
                                <ul class="text-muted m-0 p-0 ps-3" style="font-size:11px; line-height: 1.4;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" class="btn-close ms-2" onclick="closeToast(this)" aria-label="Close" style="background-size: 10px; opacity: 0.6; border: none; background-color: transparent;"></button>
                        </div>
                        <div class="dt-toast-progress bg-danger"></div>
                    </div>
                @endif
            </div>

            @yield('content')
        </div>
    </div>
</div>

<!-- Notification Modal (Pop-up Center Minimalis & Elegan) -->
<div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(0,0,0,0.04) !important;">
            <!-- Header: Bersih, Minimalis & Elegan -->
            <div class="modal-header px-4 py-3 bg-white d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9;">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="modal-title fw-bold text-navy mb-0" id="notifModalLabel" style="font-size: 15px; letter-spacing: -0.2px;">Notifikasi</h6>
                        <span class="badge rounded-pill" id="notifUnreadBadge" style="background: #f1f5f9; color: #64748b; font-size: 10.5px; font-weight: 500; padding: 2.5px 8px; border: 1px solid #e2e8f0;">0 baru</span>
                        <button type="button" class="btn btn-link p-0 text-decoration-none d-none ms-1" id="btnMarkAllRead" onclick="markAllNotificationsAsRead()" style="font-size: 11.5px; color: #2563eb; font-weight: 600;">
                            <i class="bi bi-check2-all me-0.5"></i>Tandai Dibaca
                        </button>
                    </div>
                    <div class="text-muted" style="font-size: 11.5px; margin-top: 2px;">Aktivitas sistem & pembaruan stok</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 11px; opacity: 0.55;"></button>
            </div>

            <!-- Body: List Notifikasi -->
            <div class="modal-body p-0 overflow-y-auto" id="notifList" style="max-height: 360px; min-height: 180px;">
                <div class="text-center py-5 text-muted" style="font-size:13px;">
                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                    <div>Memuat notifikasi...</div>
                </div>
            </div>

            <!-- Footer: Simple & Clean -->
            <div class="modal-footer px-4 py-2.5 bg-white d-flex justify-content-between align-items-center" style="border-top: 1px solid #f1f5f9;">
                <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 11.5px;">
                    <span class="d-inline-block rounded-circle bg-success" style="width: 8px; height: 8px; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);"></span>
                    <span class="fw-medium text-secondary">Sinkronisasi Otomatis</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 d-none" id="footerMarkReadBtn" onclick="markAllNotificationsAsRead()" style="font-size: 11.5px; font-weight: 600;">
                        <i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca
                    </button>
                    <button type="button" class="btn btn-sm btn-light border px-3 py-1.5 rounded-pill fw-semibold text-secondary shadow-none" data-bs-dismiss="modal" style="font-size: 12px; background: #f8fafc; border-color: #e2e8f0;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle for mobile menu drawer
window.openSidebarMobile = function() {
    const sidebar = document.querySelector('.dt-sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.add('show');
    if (overlay) {
        overlay.classList.add('show');
        overlay.style.setProperty('display', 'block', 'important');
    }
};

window.closeSidebarMobile = function() {
    const sidebar = document.querySelector('.dt-sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.remove('show');
    if (overlay) {
        overlay.classList.remove('show');
        overlay.style.setProperty('display', 'none', 'important');
    }
};

const sidebarToggle = document.getElementById('sidebarToggle');
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        const sidebar = document.querySelector('.dt-sidebar');
        if (sidebar && sidebar.classList.contains('show')) {
            window.closeSidebarMobile();
        } else {
            window.openSidebarMobile();
        }
    });
}
// Auto-hide alerts
document.querySelectorAll('.dt-alert').forEach(el => {
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .5s'; setTimeout(() => el.remove(), 500); }, 5000);
});

// Live ticking WIB clock
setInterval(() => {
    const clockEl = document.getElementById('liveClock');
    if (clockEl) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        clockEl.textContent = `${hours}:${minutes}:${seconds}`;
    }
}, 1000);

// Silent background poll for unread notification count (Zero page reloads / Non-kedip)
setInterval(() => {
    if (typeof loadUnreadNotifCount === 'function') {
        loadUnreadNotifCount();
    }
}, 10000);
</script>
@stack('scripts')
<script>
// ─── 3-DOT KEBAB MENU ─────────────────────────────────────────
function toggleMenu(btn) {
    const wrap = btn.closest('.dt-action-wrap');
    if (!wrap) return;
    const menu = wrap.querySelector('.dt-action-menu');
    if (!menu) return;
    const isOpen = menu.classList.contains('open');

    // tutup semua menu lain
    document.querySelectorAll('.dt-action-menu.open').forEach(m => {
        if (m !== menu) {
            m.classList.remove('open');
            const parentWrap = m.closest('.dt-action-wrap');
            if (parentWrap) parentWrap.classList.remove('dropup');
        }
    });

    if (!isOpen) {
        // Cek ruang di bawah tombol, jika mepet layar bawah jadikan dropup
        const rect = btn.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom;
        if (spaceBelow < 140 && rect.top > 140) {
            wrap.classList.add('dropup');
        } else {
            wrap.classList.remove('dropup');
        }
        menu.classList.add('open');
    } else {
        menu.classList.remove('open');
        wrap.classList.remove('dropup');
    }
}
// klik di luar = tutup semua
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dt-action-wrap')) {
        document.querySelectorAll('.dt-action-menu.open').forEach(m => {
            m.classList.remove('open');
            const parentWrap = m.closest('.dt-action-wrap');
            if (parentWrap) parentWrap.classList.remove('dropup');
        });
    }
});

// ─── DIRECT RECEIPT PRINTING (NO NEW TAB) ─────────────────────
window.printReceiptDirect = function(url) {
    let oldFrame = document.getElementById('globalPrintReceiptFrame');
    if (oldFrame) {
        oldFrame.remove();
    }

    const frame = document.createElement('iframe');
    frame.id = 'globalPrintReceiptFrame';
    frame.style.position = 'fixed';
    frame.style.right = '0';
    frame.style.bottom = '0';
    frame.style.width = '0';
    frame.style.height = '0';
    frame.style.border = '0';
    frame.style.visibility = 'hidden';
    document.body.appendChild(frame);

    frame.onload = function() {
        setTimeout(function() {
            try {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            } catch (err) {
                console.warn('Direct print blocked, fallback:', err);
                window.open(url, '_blank');
            }
        }, 250);
    };

    frame.src = url;
};

function closeToast(btn) {
    const toast = btn.closest('.dt-toast');
    if (toast) {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 400);
    }
}

// ─── NOTIFICATION BELL SYSTEM ───────────────────────────────────────────────
function clearNotifBadgeUI() {
    const notifBadge = document.getElementById('notifBadge');
    if (notifBadge) {
        notifBadge.classList.add('d-none');
        notifBadge.style.setProperty('display', 'none', 'important');
        notifBadge.textContent = '0';
    }
    const notifUnreadBadge = document.getElementById('notifUnreadBadge');
    if (notifUnreadBadge) {
        notifUnreadBadge.textContent = '0 baru';
        notifUnreadBadge.style.background = '#f1f5f9';
        notifUnreadBadge.style.color = '#64748b';
        notifUnreadBadge.style.borderColor = '#e2e8f0';
    }
    const btnMarkAllRead = document.getElementById('btnMarkAllRead');
    if (btnMarkAllRead) btnMarkAllRead.classList.add('d-none');
    const footerMarkReadBtn = document.getElementById('footerMarkReadBtn');
    if (footerMarkReadBtn) footerMarkReadBtn.classList.add('d-none');
}

async function loadUnreadNotifCount() {
    try {
        const res = await fetch('{{ route("notifications.index") }}');
        if (!res.ok) return;
        const data = await res.json();
        const notifBadge = document.getElementById('notifBadge');
        const notifUnreadBadge = document.getElementById('notifUnreadBadge');
        
        if (data.unread_count > 0) {
            if (notifBadge) {
                notifBadge.textContent = data.unread_count;
                notifBadge.classList.remove('d-none');
                notifBadge.style.removeProperty('display');
            }
            if (notifUnreadBadge) {
                notifUnreadBadge.textContent = data.unread_count + ' Baru';
                notifUnreadBadge.style.background = '#fee2e2';
                notifUnreadBadge.style.color = '#dc2626';
                notifUnreadBadge.style.borderColor = '#fecaca';
            }
        } else {
            clearNotifBadgeUI();
        }
    } catch(e) {}
}

async function fetchNotificationsAndClearBadge() {
    // 1. Langsung bersihkan angka merah di lonceng seketika tombol diklik
    clearNotifBadgeUI();

    try {
        // 2. Ambil notifikasi dari server dan tampilkan di modal
        const res = await fetch('{{ route("notifications.index") }}');
        if (res.ok) {
            const data = await res.json();
            renderNotifItems(data.notifications);
        }

        // 3. Update status di database agar semua notifikasi ditandai dibaca
        await fetch('{{ route("notifications.markRead") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });

        // 4. Pastikan badge tetap bersih setelah request selesai
        clearNotifBadgeUI();
    } catch(e) {}
}

async function markAllNotificationsAsRead() {
    clearNotifBadgeUI();
    try {
        await fetch('{{ route("notifications.markRead") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        clearNotifBadgeUI();
        document.querySelectorAll('.notif-card').forEach(el => {
            el.style.background = '#ffffff';
            el.style.border = '1px solid #f1f5f9';
            el.style.opacity = '0.85';
            const unreadDot = el.querySelector('.notif-unread-dot');
            if (unreadDot) unreadDot.remove();
        });
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', () => {
    const notifModalEl = document.getElementById('notifModal');
    if (notifModalEl) {
        notifModalEl.addEventListener('show.bs.modal', clearNotifBadgeUI);
    }
});

function renderNotifItems(items) {
    const list = document.getElementById('notifList');
    if (!list) return;

    if (!items || items.length === 0) {
        list.innerHTML = `
            <div class="text-center py-5 px-4 my-1">
                <i class="bi bi-bell-slash text-muted d-block mb-2" style="font-size: 32px; opacity: 0.35;"></i>
                <div class="fw-semibold text-dark mb-1" style="font-size: 14px;">Tidak ada notifikasi</div>
                <p class="text-muted mb-0" style="font-size: 12px; line-height: 1.5; max-width: 270px; margin: 0 auto;">
                    Semua stok kain dan aktivitas transaksi dalam kondisi normal.
                </p>
            </div>
        `;
        return;
    }

    let html = '<div class="p-3">';
    items.forEach(n => {
        let iconHtml = '';
        let badgeHtml = '';

        if (n.type === 'stok_habis') {
            iconHtml = `<div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 38px; height: 38px; background: #fee2e2; color: #dc2626;"><i class="bi bi-x-circle-fill" style="font-size: 16px;"></i></div>`;
            badgeHtml = `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle font-monospace ms-1.5 flex-shrink-0" style="font-size: 10px; font-weight:600;">Stok Habis</span>`;
        } else if (n.type === 'stok_menipis') {
            iconHtml = `<div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 38px; height: 38px; background: #fffbeb; color: #b45309;"><i class="bi bi-exclamation-triangle-fill" style="font-size: 16px;"></i></div>`;
            badgeHtml = `<span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle font-monospace ms-1.5 flex-shrink-0" style="font-size: 10px; font-weight:600;">Stok Menipis</span>`;
        } else if (n.type === 'barang_masuk') {
            iconHtml = `<div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb;"><i class="bi bi-box-arrow-in-down" style="font-size: 16px;"></i></div>`;
            badgeHtml = `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle font-monospace ms-1.5 flex-shrink-0" style="font-size: 10px; font-weight:600;">Barang Masuk</span>`;
        } else if (n.type === 'barang_keluar' || n.type === 'transaksi_baru') {
            iconHtml = `<div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 38px; height: 38px; background: #dcfce7; color: #16a34a;"><i class="bi bi-box-arrow-up" style="font-size: 16px;"></i></div>`;
            badgeHtml = `<span class="badge bg-success bg-opacity-10 text-success border border-success-subtle font-monospace ms-1.5 flex-shrink-0" style="font-size: 10px; font-weight:600;">Barang Keluar</span>`;
        } else {
            iconHtml = `<div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 38px; height: 38px; background: #f1f5f9; color: #475569;"><i class="bi bi-info-circle-fill" style="font-size: 16px;"></i></div>`;
            badgeHtml = `<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle font-monospace ms-1.5 flex-shrink-0" style="font-size: 10px; font-weight:600;">Info</span>`;
        }

        const isUnread = !n.is_read;
        const cardStyle = isUnread 
            ? 'background: #f8fafc; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.03);' 
            : 'background: #ffffff; border: 1px solid #f1f5f9; opacity: 0.85;';
        const unreadDot = isUnread 
            ? '<span class="notif-unread-dot d-inline-block rounded-circle bg-primary me-1.5 flex-shrink-0" style="width: 7px; height: 7px;" title="Belum dibaca"></span>' 
            : '';

        html += `
        <a href="${n.link || '#'}" class="notif-card d-block p-3 mb-2 rounded-3 text-decoration-none transition-all position-relative overflow-hidden" style="${cardStyle}">
            <div class="d-flex align-items-start gap-3">
                ${iconHtml}
                <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                    <div class="d-flex align-items-center justify-content-between mb-1 gap-2" style="min-width: 0;">
                        <div class="d-flex align-items-center gap-1.5 overflow-hidden me-1" style="min-width: 0; flex: 1 1 auto;">
                            ${unreadDot}
                            <span class="fw-semibold text-navy text-truncate" style="font-size: 13px;">${n.title}</span>
                            ${badgeHtml}
                        </div>
                        <span class="text-muted flex-shrink-0 ms-auto" style="font-size: 10.5px; white-space: nowrap; flex-shrink: 0;">${n.created_at ? new Date(n.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : ''}</span>
                    </div>
                    <div class="text-secondary" style="font-size: 12px; line-height: 1.45; word-break: break-word;">${n.message}</div>
                </div>
            </div>
        </a>`;
    });
    html += '</div>';
    list.innerHTML = html;
}

// Function untuk Animasi Angka Berjalan (Count-Up Animation)
window.initCounterAnimations = function() {
    const counterElements = document.querySelectorAll('.dt-stat-value, .animate-counter, [data-counter]');
    
    counterElements.forEach(el => {
        if (el.dataset.animated === "true") return;

        const originalText = (el.getAttribute('data-original-text') || el.innerText || '').trim();
        if (!originalText) return;
        if (!el.hasAttribute('data-original-text')) {
            el.setAttribute('data-original-text', originalText);
        }

        const smallEl = el.querySelector('small');
        const smallHTML = smallEl ? smallEl.outerHTML : '';
        const smallText = smallEl ? smallEl.innerText : '';

        const isRp = originalText.startsWith('Rp');
        
        let numStr = originalText.replace(/^Rp\s*/i, '');
        if (smallText) {
            numStr = numStr.replace(smallText, '');
        }
        numStr = numStr.trim();

        let suffixText = '';
        const suffixMatch = numStr.match(/\s*([a-zA-Z\/]+)$/);
        if (suffixMatch) {
            suffixText = suffixMatch[0];
            numStr = numStr.replace(suffixMatch[0], '').trim();
        }

        let targetVal = 0;
        let decimals = 0;
        let isIndo = false;

        if (numStr.includes('.') && numStr.includes(',')) {
            if (numStr.lastIndexOf('.') > numStr.lastIndexOf(',')) {
                targetVal = parseFloat(numStr.replace(/,/g, ''));
                decimals = (numStr.split('.')[1] || '').length;
            } else {
                isIndo = true;
                targetVal = parseFloat(numStr.replace(/\./g, '').replace(',', '.'));
                decimals = (numStr.split(',')[1] || '').length;
            }
        } else if (numStr.includes('.')) {
            const parts = numStr.split('.');
            if (parts.length > 2 || (parts.length === 2 && parts[1].length === 3 && (isRp || parts[0].length <= 3))) {
                isIndo = true;
                targetVal = parseFloat(numStr.replace(/\./g, ''));
                decimals = 0;
            } else {
                targetVal = parseFloat(numStr);
                decimals = parts[1] ? parts[1].length : 0;
            }
        } else if (numStr.includes(',')) {
            const parts = numStr.split(',');
            if (parts.length > 2 || (parts.length === 2 && parts[1].length === 3)) {
                targetVal = parseFloat(numStr.replace(/,/g, ''));
                decimals = 0;
            } else {
                isIndo = true;
                targetVal = parseFloat(numStr.replace(',', '.'));
                decimals = parts[1] ? parts[1].length : 0;
            }
        } else {
            targetVal = parseFloat(numStr);
            decimals = 0;
        }

        if (isNaN(targetVal) || targetVal === 0) return;
        el.dataset.animated = "true";

        const duration = 200;
        const startTime = performance.now();

        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 3);
            const currentVal = targetVal * ease;

            let formattedNum = '';
            if (isRp || isIndo) {
                const fixed = currentVal.toFixed(decimals);
                const parts = fixed.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                formattedNum = parts.join(',');
            } else {
                const fixed = currentVal.toFixed(decimals);
                const parts = fixed.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                formattedNum = parts.join('.');
            }

            let out = '';
            if (isRp) out += 'Rp ';
            out += formattedNum;
            if (suffixText) out += suffixText;
            if (smallHTML) out += ' ' + smallHTML;

            el.innerHTML = out;

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }

        requestAnimationFrame(step);
    });
};

document.addEventListener('DOMContentLoaded', () => {
    loadUnreadNotifCount();
    setInterval(loadUnreadNotifCount, 30000); // refresh every 30s

    // Jalankan animasi angka statistik
    window.initCounterAnimations();

    // Animasi Progress Bar saat Pindah Halaman / Menu
    const progressBar = document.getElementById('dt-page-progress');

    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || this.getAttribute('target') === '_blank' || this.hasAttribute('download') || e.ctrlKey || e.metaKey) {
                return;
            }

            if (progressBar) {
                progressBar.classList.add('loading');
                progressBar.style.width = '70%';
            }
        });
    });
});

window.addEventListener('pageshow', () => {
    const progressBar = document.getElementById('dt-page-progress');
    if (progressBar) {
        progressBar.style.width = '100%';
        setTimeout(() => {
            progressBar.classList.remove('loading');
            progressBar.style.width = '0%';
        }, 250);
    }
});

// Instant Real-Time Client-Side Search (0ms Latency, Zero Page Reload)
document.addEventListener('DOMContentLoaded', () => {
    function escapeHtml(str) {
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // Sembunyikan popup riwayat pencarian bawaan browser (Browser History Autocomplete Popup)
    document.querySelectorAll('input').forEach(input => {
        const isSearchField = input.name === 'search' || 
                             input.type === 'search' || 
                             input.classList.contains('dt-search-input') || 
                             (input.placeholder && (
                                input.placeholder.toLowerCase().includes('cari') || 
                                input.placeholder.toLowerCase().includes('ketik') ||
                                input.placeholder.toLowerCase().includes('scan')
                             ));
        if (isSearchField) {
            input.setAttribute('autocomplete', 'off');
            if (input.form) {
                input.form.setAttribute('autocomplete', 'off');
            }
        }
    });

    document.addEventListener('input', (e) => {
        const input = e.target;
        if (!input || input.tagName !== 'INPUT') return;

        // Abaikan form create/edit atau data entry (POST form atau modal)
        const form = input.closest('form');
        if (form && form.method && form.method.toLowerCase() === 'post') return;
        if (input.closest('.modal') || input.closest('#itemsTable')) return;

        const isSearchField = input.name === 'search' || 
                             input.type === 'search' || 
                             input.id === 'searchInput' ||
                             input.classList.contains('dt-search-input') || 
                             (input.placeholder && input.placeholder.toLowerCase().includes('cari'));

        if (!isSearchField) return;

        const query = input.value.toLowerCase().trim();
        const container = input.closest('.dt-card') || input.closest('.dt-page') || document;
        const tables = container.querySelectorAll('table.dt-table, table.table');

        if (tables.length > 0) {
            tables.forEach(table => {
                if (table.closest('form') && table.closest('form').method && table.closest('form').method.toLowerCase() === 'post') return;

                const tbody = table.querySelector('tbody');
                if (!tbody) return;

                const rows = Array.from(tbody.querySelectorAll('tr:not(.no-search-results):not(#noResultRow)'));
                let visibleCount = 0;

                rows.forEach(row => {
                    if (row.classList.contains('empty-row') || row.querySelector('.empty-state')) return;

                    const text = row.innerText.toLowerCase();
                    if (!query || text.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Tampilkan pesan jika tidak ada data yang cocok
                let noResultRow = tbody.querySelector('.no-search-results');
                if (visibleCount === 0 && query !== '') {
                    if (!noResultRow) {
                        const colCount = table.querySelectorAll('thead th').length || 6;
                        noResultRow = document.createElement('tr');
                        noResultRow.className = 'no-search-results';
                        noResultRow.innerHTML = `
                            <td colspan="${colCount}" class="text-center py-4 text-muted">
                                <i class="bi bi-search fs-4 d-block mb-1 opacity-50"></i>
                                <span style="font-size: 12.5px;">Tidak ada data yang cocok dengan pencarian "<strong>${escapeHtml(query)}</strong>"</span>
                            </td>`;
                        tbody.appendChild(noResultRow);
                    } else {
                        const strong = noResultRow.querySelector('strong');
                        if (strong) strong.innerText = query;
                        noResultRow.style.display = '';
                    }
                } else if (noResultRow) {
                    noResultRow.style.display = 'none';
                }

                // Sembunyikan pagination saat filter lokal aktif
                const pagination = container.querySelector('.pagination, #paginationContainer, nav[aria-label*="pagination"]');
                if (pagination) {
                    pagination.style.display = query ? 'none' : '';
                }
            });
        }
    });
});

// Auto close toasts after 4 seconds
document.addEventListener('DOMContentLoaded', () => {
    const toasts = document.querySelectorAll('.dt-toast');
    toasts.forEach(toast => {
        setTimeout(() => {
            if (toast && toast.classList.contains('show')) {
                toast.classList.remove('show');
                toast.classList.add('hide');
                setTimeout(() => toast.remove(), 400);
            }
        }, 4000);
    });

    // Registrasi PWA Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(reg => console.log('PWA Service Worker terdaftar!', reg))
            .catch(err => console.error('Gagal mendaftarkan PWA Service Worker', err));
    }
});

// Teleport modal apapun ke document.body agar tidak terjebak stacking context / backdrop
document.addEventListener('show.bs.modal', function (event) {
    const modal = event.target;
    if (modal && modal.classList.contains('modal') && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
});
</script>

</body>
</html>
