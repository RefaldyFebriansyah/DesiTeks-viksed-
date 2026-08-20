@php
    $role = auth()->user()->role;
    
    // Tentukan menu aktif server-side untuk mencegah flicker saat berpindah halaman
    $activeMenu = '';
    if ($role === 'admin') {
        if (request()->routeIs(['admin.fabrics*', 'admin.categories*', 'admin.suppliers*', 'admin.customers*'])) {
            $activeMenu = 'master-data';
        } elseif (request()->routeIs(['admin.reports*', 'admin.transactions*', 'admin.stock-movements*', 'admin.stocks*'])) {
            $activeMenu = 'laporan-audit';
        } elseif (request()->routeIs(['admin.users*', 'admin.audit-logs*', 'admin.settings*'])) {
            $activeMenu = 'sistem-pengaturan';
        }
    } elseif ($role === 'gudang') {
        if (request()->routeIs(['gudang.stocks*'])) {
            $activeMenu = 'gudang-stok';
        } elseif (request()->routeIs(['gudang.suppliers*', 'gudang.incoming-goods*'])) {
            $activeMenu = 'gudang-pengadaan';
        }
    } elseif ($role === 'kasir') {
        if (request()->routeIs(['kasir.transactions*', 'kasir.income*'])) {
            $activeMenu = 'kasir-transaksi';
        }
    }
@endphp

{{-- Overlay for mobile --}}
<div class="d-lg-none" id="sidebarOverlay" style="display:none!important;position:fixed;inset:0;background:rgba(15,39,68,.4);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px);z-index:99"
     onclick="closeSidebarMobile()"></div>

<aside class="dt-sidebar">
    {{-- Brand --}}
    <div class="dt-sidebar-brand d-flex align-items-center justify-content-between" style="padding:18px 20px;border-bottom:1px solid rgba(255,255,255,.08);">
        <a href="#" class="d-flex align-items-center gap-2" style="text-decoration:none">
            <img src="{{ asset('images/logo_icon_light.png') }}" alt="DesiTeks Icon" style="height:42px;width:auto;object-fit:contain;flex-shrink:0">
            <div>
                <span style="font-size:21px;font-weight:800;color:var(--dt-white);letter-spacing:.5px;display:block;line-height:1.1">
                    Desi<span style="color:var(--dt-gold)">Teks</span>
                </span>
                <span style="font-size:9.5px;color:rgba(255,255,255,.5);letter-spacing:.3px;display:block;margin-top:2px;white-space:nowrap">
                    Kain Berkualitas, Gaya Tanpa Batas
                </span>
            </div>
        </a>
        {{-- Close button for mobile menu drawer --}}
        <button type="button" class="btn d-lg-none p-1 border-0" id="sidebarCloseBtn" style="color: rgba(255,255,255,.65); background: transparent;" onclick="closeSidebarMobile()">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>

    {{-- Script Sinkron Instan untuk Mencegah Flicker Dropdown Terbuka di localStorage --}}
    <script>
    (function() {
        try {
            var openMenus = JSON.parse(localStorage.getItem('dt_open_menus')) || [];
            var active = "{{ $activeMenu }}";
            if (active && openMenus.indexOf(active) === -1) {
                openMenus.push(active);
                localStorage.setItem('dt_open_menus', JSON.stringify(openMenus));
            }
            openMenus.forEach(function(id) {
                var btn = document.querySelector('.dt-nav-dropdown-btn[data-menu-id="' + id + '"]');
                if (btn) {
                    btn.classList.add('open');
                    var container = btn.nextElementSibling;
                    if (container) {
                        container.classList.add('show');
                    }
                }
            });
        } catch(e) {}
    })();
    </script>

    <nav class="dt-nav">

        @if($role === 'admin')
        {{-- ======== ADMIN MENU ======== --}}
        <div class="dt-nav-section">Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="dt-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="dt-nav-section">Menu Utama</div>

        <!-- Master Data Dropdown -->
        <div>
            <button type="button" class="dt-nav-dropdown-btn {{ $activeMenu === 'master-data' ? 'open active' : '' }}" data-menu-id="master-data">
                <span><i class="bi bi-folder-fill me-2"></i> Master Data</span>
                <i class="bi bi-chevron-right dt-chevron"></i>
            </button>
            <div class="dt-nav-dropdown-container {{ $activeMenu === 'master-data' ? 'show' : '' }}">
                <a href="{{ route('admin.fabrics.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.fabrics*') ? 'active' : '' }}">
                    <i class="bi bi-grid-3x3-gap"></i> Data Kain
                </a>
                <a href="{{ route('admin.categories.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Kategori Kain
                </a>
                <a href="{{ route('admin.suppliers.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.suppliers*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i> Supplier
                </a>
                <a href="{{ route('admin.customers.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Pelanggan
                </a>
            </div>
        </div>

        <!-- Laporan & Analitik Dropdown -->
        <div>
            <button type="button" class="dt-nav-dropdown-btn {{ $activeMenu === 'laporan-audit' ? 'open active' : '' }}" data-menu-id="laporan-audit">
                <span><i class="bi bi-bar-chart-line-fill me-2"></i> Laporan & Audit</span>
                <i class="bi bi-chevron-right dt-chevron"></i>
            </button>
            <div class="dt-nav-dropdown-container {{ $activeMenu === 'laporan-audit' ? 'show' : '' }}">
                <a href="{{ route('admin.reports.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i> Laporan Penjualan
                </a>
                <a href="{{ route('admin.transactions.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff"></i> Riwayat Transaksi
                </a>
                <a href="{{ route('admin.stock-movements.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.stock-movements*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i> Riwayat Pergerakan Stok
                </a>
                <a href="{{ route('admin.stocks.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.stocks*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Laporan Stok Kain
                </a>
            </div>
        </div>

        <!-- Sistem & Keamanan Dropdown -->
        <div>
            <button type="button" class="dt-nav-dropdown-btn {{ $activeMenu === 'sistem-pengaturan' ? 'open active' : '' }}" data-menu-id="sistem-pengaturan">
                <span><i class="bi bi-gear-fill me-2"></i> Pengaturan & Sistem</span>
                <i class="bi bi-chevron-right dt-chevron"></i>
            </button>
            <div class="dt-nav-dropdown-container {{ $activeMenu === 'sistem-pengaturan' ? 'show' : '' }}">
                <a href="{{ route('admin.users.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Kelola Pengguna
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Log Aktivitas
                </a>
                <a href="{{ route('admin.settings.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i> Pengaturan Toko
                </a>
            </div>
        </div>

        @elseif($role === 'gudang')
        {{-- ======== GUDANG MENU ======== --}}
        <div class="dt-nav-section">Utama</div>
        <a href="{{ route('gudang.dashboard') }}" class="dt-nav-link {{ request()->routeIs('gudang.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="dt-nav-section">Manajemen Gudang</div>
        
        <!-- Inventaris Dropdown -->
        <div>
            <button type="button" class="dt-nav-dropdown-btn {{ $activeMenu === 'gudang-stok' ? 'open active' : '' }}" data-menu-id="gudang-stok">
                <span><i class="bi bi-box-seam-fill me-2"></i> Stok & Kain</span>
                <i class="bi bi-chevron-right dt-chevron"></i>
            </button>
            <div class="dt-nav-dropdown-container {{ $activeMenu === 'gudang-stok' ? 'show' : '' }}">
                <a href="{{ route('gudang.stocks.index') }}" class="dt-nav-sublink {{ request()->routeIs('gudang.stocks*') ? 'active' : '' }}">
                    <i class="bi bi-box"></i> Cek Stok Kain
                </a>
            </div>
        </div>

        <!-- Pengadaan Dropdown -->
        <div>
            <button type="button" class="dt-nav-dropdown-btn {{ $activeMenu === 'gudang-pengadaan' ? 'open active' : '' }}" data-menu-id="gudang-pengadaan">
                <span><i class="bi bi-truck-flatbed me-2"></i> Pengadaan</span>
                <i class="bi bi-chevron-right dt-chevron"></i>
            </button>
            <div class="dt-nav-dropdown-container {{ $activeMenu === 'gudang-pengadaan' ? 'show' : '' }}">
                <a href="{{ route('gudang.suppliers.index') }}" class="dt-nav-sublink {{ request()->routeIs('gudang.suppliers*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i> Kelola Supplier
                </a>
                <a href="{{ route('gudang.incoming-goods.index') }}" class="dt-nav-sublink {{ request()->routeIs('gudang.incoming-goods*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-down-square"></i> Input Barang Masuk
                </a>
            </div>
        </div>

        @elseif($role === 'kasir')
        {{-- ======== KASIR MENU ======== --}}
        <div class="dt-nav-section">Utama</div>
        <a href="{{ route('kasir.dashboard') }}" class="dt-nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="dt-nav-section">Layanan Kasir</div>
        <a href="{{ route('kasir.sales.pos') }}" class="dt-nav-link {{ request()->routeIs('kasir.sales*') ? 'active' : '' }}">
            <i class="bi bi-cart3"></i> POS Penjualan (Kasir)
        </a>

        <!-- Transaksi & Income Dropdown -->
        <div>
            <button type="button" class="dt-nav-dropdown-btn {{ $activeMenu === 'kasir-transaksi' ? 'open active' : '' }}" data-menu-id="kasir-transaksi">
                <span><i class="bi bi-wallet2 me-2"></i> Transaksi Hari Ini</span>
                <i class="bi bi-chevron-right dt-chevron"></i>
            </button>
            <div class="dt-nav-dropdown-container {{ $activeMenu === 'kasir-transaksi' ? 'show' : '' }}">
                <a href="{{ route('kasir.transactions.index') }}" class="dt-nav-sublink {{ request()->routeIs('kasir.transactions*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Riwayat Penjualan
                </a>
                <a href="{{ route('kasir.income.index') }}" class="dt-nav-sublink {{ request()->routeIs('kasir.income*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i> Rekap Pendapatan
                </a>
            </div>
        </div>

        <a href="{{ route('kasir.stocks.index') }}" class="dt-nav-link {{ request()->routeIs('kasir.stocks*') ? 'active' : '' }}">
            <i class="bi bi-search"></i> Cek Stok Kain
        </a>
        @endif

    </nav>

    {{-- Footer --}}
    <div class="dt-sidebar-footer">
        <div class="dt-sidebar-user d-flex align-items-center gap-2 mb-2">
            <div style="width:34px;height:34px;background:rgba(201,168,76,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--dt-gold);font-weight:700;font-size:13px;flex-shrink:0">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>
            <div>
                <div style="font-size:13px;font-weight:500;color:rgba(255,255,255,.85)">{{ auth()->user()->name }}</div>
                <small>{{ ucfirst(auth()->user()->role) }}</small>
            </div>
        </div>
        <!-- PWA Install Button -->
        <button id="btnInstallPWA" class="dt-nav-link w-100 border-0 d-none" style="background:none;color:var(--dt-gold);text-align:left;cursor:pointer;margin-bottom:8px;">
            <i class="bi bi-download text-gold"></i> Unduh Aplikasi
        </button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dt-nav-link w-100 border-0" style="background:none;color:rgba(255,100,100,.7);text-align:left;cursor:pointer">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown toggles click handler
    document.querySelectorAll('.dt-nav-dropdown-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.nextElementSibling;
            const menuId = this.dataset.menuId;
            const isOpen = this.classList.contains('open');
            
            let currentOpen = [];
            try {
                currentOpen = JSON.parse(localStorage.getItem('dt_open_menus')) || [];
            } catch(e) {
                currentOpen = [];
            }

            if (isOpen) {
                this.classList.remove('open');
                if (container) container.classList.remove('show');
                
                // Hapus dari list
                currentOpen = currentOpen.filter(id => id !== menuId);
            } else {
                this.classList.add('open');
                if (container) container.classList.add('show');
                
                // Tambah ke list jika belum ada
                if (menuId && !currentOpen.includes(menuId)) {
                    currentOpen.push(menuId);
                }
            }
            
            localStorage.setItem('dt_open_menus', JSON.stringify(currentOpen));
        });
    });
});
</script>
