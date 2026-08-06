<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DesiTeks') — DesiTeks</title>
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
                <span class="text-muted" style="font-size:12px">
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

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="dt-alert dt-alert-success">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="dt-alert dt-alert-danger">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="dt-alert dt-alert-danger">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle for mobile
const sidebarToggle = document.getElementById('sidebarToggle');
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        document.querySelector('.dt-sidebar').classList.toggle('show');
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
</body>
</html>
