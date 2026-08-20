<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DesiTeks') — DesiTeks</title>
    
    <!-- PWA Settings -->
    <meta name="theme-color" content="#0f2744">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="DesiTeks">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_icon_light.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/desiteks.css') }}">
    @stack('styles')
</head>
<body>
<div class="dt-wrapper">

    {{-- SIDEBAR --}}
    @include('partials.sidebar')

    {{-- MAIN --}}
    <div class="dt-main">

        {{-- TOPBAR --}}
        <div class="dt-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm d-lg-none" id="sidebarToggle" style="color:var(--dt-navy)">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="dt-topbar-title">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="dt-topbar-actions">
                <span class="text-muted d-none d-lg-inline" style="font-size:12px">
                    <i class="bi bi-clock me-1"></i><span id="liveClock">{{ now()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y H:i:s') }}</span> WIB
                </span>
                <div class="dropdown">
                    <button class="dt-btn dt-btn-outline dt-btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><span class="dropdown-item-text text-muted" style="font-size:12px">
                            Role: <strong>{{ ucfirst(auth()->user()->role) }}</strong>
                        </span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle for mobile menu drawer
window.openSidebarMobile = function() {
    const sidebar = document.querySelector('.dt-sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.add('show');
    if (overlay) overlay.style.setProperty('display', 'block', 'important');
};

window.closeSidebarMobile = function() {
    const sidebar = document.querySelector('.dt-sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.remove('show');
    if (overlay) overlay.style.setProperty('display', 'none', 'important');
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
        const options = { timeZone: 'Asia/Jakarta', weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        clockEl.textContent = new Intl.DateTimeFormat('id-ID', options).format(now);
    }
}, 1000);

// Smart Realtime Live Polling: Auto-refreshes data list pages every 12s if user is idle
let isUserInteracting = false;
['input', 'change', 'keydown', 'mousedown'].forEach(evt => {
    document.addEventListener(evt, () => {
        isUserInteracting = true;
        clearTimeout(window.idleTimer);
        window.idleTimer = setTimeout(() => { isUserInteracting = false; }, 15000);
    });
});

// Auto-refresh monitoring pages every 12 seconds if not filling form
const currentPath = window.location.pathname;
const isFormPage = currentPath.includes('/create') || currentPath.includes('/edit') || currentPath.includes('/pos');

if (!isFormPage) {
    setInterval(() => {
        if (!isUserInteracting && !document.querySelector('.modal.show')) {
            window.location.reload();
        }
    }, 12000);
}
</script>
@stack('scripts')
<script>
// ─── 3-DOT KEBAB MENU ─────────────────────────────────────────
function toggleMenu(btn) {
    const menu = btn.nextElementSibling;
    const isOpen = menu.classList.contains('open');

    // tutup semua menu lain
    document.querySelectorAll('.dt-action-menu.open').forEach(m => m.classList.remove('open'));

    if (!isOpen) {
        // hitung posisi dari tombol (fixed relatif ke viewport)
        const rect = btn.getBoundingClientRect();
        menu.style.top  = (rect.bottom + 4) + 'px';
        menu.style.left = '';
        // cek apakah keluar kanan layar
        const menuW = 160;
        if (rect.right + menuW > window.innerWidth) {
            menu.style.right = (window.innerWidth - rect.right) + 'px';
            menu.style.left  = '';
        } else {
            menu.style.left  = rect.left + 'px';
            menu.style.right = '';
        }
        menu.classList.add('open');
    }
}
// klik di luar = tutup semua
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dt-action-wrap')) {
        document.querySelectorAll('.dt-action-menu.open').forEach(m => m.classList.remove('open'));
    }
});
// scroll = tutup semua menu (biar tidak melayang)
document.addEventListener('scroll', function() {
    document.querySelectorAll('.dt-action-menu.open').forEach(m => m.classList.remove('open'));
}, true);

function closeToast(btn) {
    const toast = btn.closest('.dt-toast');
    if (toast) {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 400);
    }
}

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

    // Penanganan Install PWA
    let deferredPrompt;
    const btnInstallPWA = document.getElementById('btnInstallPWA');

    window.addEventListener('beforeinstallprompt', (e) => {
        // Mencegah mini-infobar default browser muncul
        e.preventDefault();
        // Simpan event prompt
        deferredPrompt = e;
        // Munculkan tombol download aplikasi di sidebar
        if (btnInstallPWA) {
            btnInstallPWA.classList.remove('d-none');
        }
    });

    if (btnInstallPWA) {
        btnInstallPWA.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            // Tampilkan prompt instalasi
            deferredPrompt.prompt();
            // Tunggu pilihan user
            const { outcome } = await deferredPrompt.userChoice;
            // Reset prompt
            deferredPrompt = null;
            // Sembunyikan tombol
            btnInstallPWA.classList.add('d-none');
        });
    }

    window.addEventListener('appinstalled', (event) => {
        deferredPrompt = null;
        if (btnInstallPWA) {
            btnInstallPWA.classList.add('d-none');
        }
        // Tampilkan notifikasi melayang sukses
        if (typeof showToastAlert === 'function') {
            showToastAlert('Instalasi Sukses', 'Aplikasi DesiTeks berhasil terpasang di perangkat Anda!', 'success');
        }
    });
});
</script>

</body>
</html>
