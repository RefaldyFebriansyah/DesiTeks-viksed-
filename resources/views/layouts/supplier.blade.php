@php
    $supplierUser = auth()->user();
    $supplierComp = $supplierUser?->supplier;
    $compName = $supplierComp?->nama_supplier ?? $supplierUser?->name ?? 'Mitra Supplier';
    $compCode = $supplierComp?->kode_supplier ?? 'SUP';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Supplier') — {{ $storeName }} Partner</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_icon_light.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_icon_light.png') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sup-primary: #2563eb;
            --sup-primary-dark: #1d4ed8;
            --sup-body-bg: #f8fafc;
            --sup-card-bg: #ffffff;
            --sup-text: #0f172a;
            --sup-muted: #64748b;
            --sup-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--sup-body-bg);
            color: var(--sup-text);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            letter-spacing: -0.015em;
        }

        /* Pop-Up Notification Toast & Countdown Line */
        .toast-card-custom {
            position: relative !important;
            overflow: hidden !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
            pointer-events: auto !important;
        }
        .toast-progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3.5px;
            width: 100%;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            animation: toastCountdown 4s linear forwards;
        }
        @keyframes toastCountdown {
            0% { width: 100%; }
            100% { width: 0%; }
        }

        /* Glassmorphism Sticky Top Header Navbar */
        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            padding: 10px 0;
            transition: all 0.2s ease;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03);
        }

        .site-nav-link {
            color: #475569 !important;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 18px !important;
            border-radius: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .site-nav-link:hover {
            color: #0f172a !important;
            background-color: #f1f5f9;
        }

        .site-nav-link.active {
            color: #2563eb !important;
            background-color: #eff6ff;
            font-weight: 700;
            box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.15);
        }

        .btn-brand-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff !important;
            font-weight: 600;
            font-size: 14px;
            padding: 9px 22px;
            border-radius: 12px;
            border: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }

        .btn-brand-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-brand-outline {
            background-color: #ffffff;
            color: #1e293b !important;
            font-weight: 600;
            font-size: 14px;
            padding: 9px 18px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        .btn-brand-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a !important;
            transform: translateY(-1px);
        }

        /* Content Area */
        .sup-main-wrapper {
            padding-top: 85px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sup-content {
            padding: 24px 0 48px;
            flex-grow: 1;
        }

        /* Modern Elevated Card System */
        .sup-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .sup-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 12px 30px -5px rgba(15, 23, 42, 0.08);
            transform: translateY(-3px);
        }

        .sup-btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border: none;
            padding: 9px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13.5px;
            box-shadow: 0 3px 10px rgba(37, 99, 235, 0.28);
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .sup-btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.38);
            transform: translateY(-1px);
        }

        /* User dropdown button fix */
        .user-menu-btn {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            padding: 4px 10px 4px 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .user-menu-btn:hover {
            border-color: #cbd5e1;
            box-shadow: 0 3px 10px rgba(0,0,0,0.06);
        }

        @media (max-width: 576px) {
            .btn-brand-primary span, .btn-brand-outline span {
                display: none;
            }
            .btn-brand-primary, .btn-brand-outline {
                padding: 8px 12px;
            }
            .navbar-brand span {
                font-size: 18px !important;
            }
            .navbar-brand img {
                height: 38px !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sticky Glassmorphic Top Navbar -->
    <header class="site-header">
        <div class="container-xl">
            <div class="d-flex align-items-center justify-content-between">
                
                {{-- Left: Brand Logo --}}
                <div class="d-flex align-items-center gap-3">
                    <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('supplier.dashboard') }}">
                        <img src="{{ asset('images/logo_kainkita_transparent.png') }}" alt="KainKita Logo" style="height: 42px; width: auto; object-fit: contain;">
                        <span style="font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.03em; line-height: 1;">
                            {!! $formattedStoreName !!}
                        </span>
                    </a>
                </div>

                {{-- Center: Simplified Core Navigation Menu Links --}}
                <nav class="d-none d-lg-flex align-items-center gap-1">
                    <a class="site-nav-link {{ (request()->routeIs('supplier.landing') || request()->routeIs('supplier.dashboard')) ? 'active' : '' }}" href="{{ route('supplier.dashboard') }}">
                        <span>Beranda</span>
                    </a>
                    <a class="site-nav-link {{ request()->routeIs('supplier.delivery-orders.create') ? 'active' : '' }}" href="{{ route('supplier.delivery-orders.create') }}">
                        <span>Buat Surat Jalan</span>
                    </a>
                    <a class="site-nav-link {{ request()->routeIs('supplier.delivery-orders.index') || request()->routeIs('supplier.delivery-orders.show') ? 'active' : '' }}" href="{{ route('supplier.delivery-orders.index') }}">
                        <span>Daftar Pengiriman</span>
                    </a>
                </nav>

                {{-- Right: Actions & User Dropdown Menu --}}
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('supplier.delivery-orders.create') }}" class="btn-brand-primary">
                        <i class="bi bi-plus-lg"></i>
                        <span>Kirim Surat Jalan</span>
                    </a>

                    @if($supplierUser)
                    <div class="dropdown ms-1">
                        <button class="user-menu-btn" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width:34px;height:34px;font-size:13px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 2px 6px rgba(37,99,235,0.3);">
                                {{ strtoupper(substr($supplierUser->name, 0, 1)) }}
                            </div>
                            <div class="d-none d-md-block text-start lh-sm" style="max-width:140px;">
                                <div class="small fw-bold text-truncate text-dark">{{ $supplierUser->name }}</div>
                                <div class="text-muted text-truncate" style="font-size:10.5px;">{{ $compName }}</div>
                            </div>
                            <i class="bi bi-chevron-down text-muted" style="font-size:11px;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="border-radius:16px; font-size:13.5px; min-width:230px;">
                            <li class="px-3 py-2 border-bottom bg-light rounded-top">
                                <div class="fw-bold text-dark text-truncate">{{ $compName }}</div>
                                <div class="text-muted small text-truncate">{{ $supplierUser->email }}</div>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mt-1 fw-bold" style="font-size:10px;">Kode: {{ $compCode }}</span>
                            </li>
                            <li><a class="dropdown-item py-2 mt-1" href="{{ route('supplier.profile.index') }}"><i class="bi bi-building me-2 text-primary"></i> Profil Perusahaan</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('supplier.delivery-orders.create') }}"><i class="bi bi-file-earmark-plus me-2 text-primary"></i> Buat Surat Jalan Baru</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('supplier.delivery-orders.index') }}"><i class="bi bi-truck me-2 text-primary"></i> Daftar Pengiriman Saya</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger fw-semibold">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout / Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <div class="d-flex align-items-center gap-2 ms-1">
                        <a href="{{ route('login') }}" class="btn-brand-outline">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Masuk</span>
                        </a>
                        <a href="{{ route('register') }}" class="btn-brand-outline d-none d-sm-inline-flex">
                            <i class="bi bi-person-plus"></i>
                            <span>Daftar</span>
                        </a>
                    </div>
                    @endif

                    {{-- Mobile menu toggle --}}
                    <button class="btn btn-outline-secondary d-lg-none p-2 px-3 rounded-3 border ms-1" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavCollapse">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                </div>

            </div>

            {{-- Mobile Nav Collapse Drawer --}}
            <div class="collapse d-lg-none mt-3 pt-3 border-top" id="mobileNavCollapse">
                <nav class="d-flex flex-column gap-1">
                    <a class="site-nav-link {{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}" href="{{ route('supplier.dashboard') }}">
                        <i class="bi bi-house me-2"></i> Beranda
                    </a>
                    <a class="site-nav-link {{ request()->routeIs('supplier.delivery-orders.create') ? 'active' : '' }}" href="{{ route('supplier.delivery-orders.create') }}">
                        <i class="bi bi-plus-circle me-2"></i> Buat Surat Jalan Baru
                    </a>
                    <a class="site-nav-link {{ request()->routeIs('supplier.delivery-orders.index') ? 'active' : '' }}" href="{{ route('supplier.delivery-orders.index') }}">
                        <i class="bi bi-truck me-2"></i> Daftar Pengiriman Saya
                    </a>
                </nav>
            </div>

        </div>
    </header>

    <!-- Main Wrapper -->
    <div class="sup-main-wrapper">
        <!-- Floating Top-Right Pop-Up Notifications (Modern Soft Badge Toast) -->
        <div class="position-fixed top-0 end-0 p-3 mt-4" style="z-index: 1095; max-width: 440px; width: calc(100% - 32px); pointer-events: none;">
            @if(session('success'))
                <div class="alert alert-dismissible fade show bg-white border-0 shadow-lg d-flex align-items-start gap-3 p-3 mb-3 rounded-4 align-items-center toast-card-custom" role="alert">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-check-lg fs-5"></i>
                    </div>
                    <div class="flex-grow-1 me-2" style="min-width: 0;">
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px; letter-spacing: -0.01em;">Berhasil!</h6>
                        <div class="text-secondary small mb-0" style="font-size: 12.5px; line-height: 1.45;">{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close position-static p-2 opacity-50 flex-shrink-0" data-bs-dismiss="alert" aria-label="Close" style="font-size: 10px;"></button>
                    <div class="toast-progress-bar bg-success"></div>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-dismissible fade show bg-white border-0 shadow-lg d-flex align-items-start gap-3 p-3 mb-3 rounded-4 align-items-center toast-card-custom" role="alert">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-info-lg fs-5"></i>
                    </div>
                    <div class="flex-grow-1 me-2" style="min-width: 0;">
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px; letter-spacing: -0.01em;">Informasi</h6>
                        <div class="text-secondary small mb-0" style="font-size: 12.5px; line-height: 1.45;">{{ session('info') }}</div>
                    </div>
                    <button type="button" class="btn-close position-static p-2 opacity-50 flex-shrink-0" data-bs-dismiss="alert" aria-label="Close" style="font-size: 10px;"></button>
                    <div class="toast-progress-bar bg-primary"></div>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-dismissible fade show bg-white border-0 shadow-lg d-flex align-items-start gap-3 p-3 mb-3 rounded-4 align-items-center toast-card-custom" role="alert">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-exclamation-lg fs-5"></i>
                    </div>
                    <div class="flex-grow-1 me-2" style="min-width: 0;">
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px; letter-spacing: -0.01em;">Peringatan!</h6>
                        <div class="text-secondary small mb-0" style="font-size: 12.5px; line-height: 1.45;">{{ session('warning') }}</div>
                    </div>
                    <button type="button" class="btn-close position-static p-2 opacity-50 flex-shrink-0" data-bs-dismiss="alert" aria-label="Close" style="font-size: 10px;"></button>
                    <div class="toast-progress-bar bg-warning"></div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-dismissible fade show bg-white border-0 shadow-lg d-flex align-items-start gap-3 p-3 mb-3 rounded-4 align-items-center toast-card-custom" role="alert">
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-x-lg fs-5"></i>
                    </div>
                    <div class="flex-grow-1 me-2" style="min-width: 0;">
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px; letter-spacing: -0.01em;">Gagal!</h6>
                        <div class="text-secondary small mb-0" style="font-size: 12.5px; line-height: 1.45;">{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close position-static p-2 opacity-50 flex-shrink-0" data-bs-dismiss="alert" aria-label="Close" style="font-size: 10px;"></button>
                    <div class="toast-progress-bar bg-danger"></div>
                </div>
            @endif
        </div>

        <main class="sup-content">
            <div class="container-xl">
                @yield('content')
            </div>
        </main>

        <footer class="py-4 bg-white border-top mt-auto">
            <div class="container-xl text-center text-muted small">
                &copy; {{ date('Y') }} <strong>{{ $storeName }}</strong>. Sistem Penerimaan & Logistik Kain.
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const alerts = document.querySelectorAll('.position-fixed .alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.classList.contains('show')) {
                        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        if (bsAlert) bsAlert.close();
                    }
                }, 4000);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
