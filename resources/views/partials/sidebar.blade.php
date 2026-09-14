@php
    $role = auth()->user()->role;
    
    // Tentukan menu aktif server-side untuk mencegah flicker saat berpindah halaman
    $activeMenu = '';
    if ($role === 'admin') {
        if (request()->routeIs(['admin.fabrics*', 'admin.categories*', 'admin.suppliers*', 'admin.customers*'])) {
            $activeMenu = 'master-data';
        } elseif (request()->routeIs(['admin.reports*', 'admin.transactions*', 'admin.stock-movements*', 'admin.stocks*', 'admin.incoming-goods*'])) {
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
    }

    $rawName   = auth()->user()->name ?? 'User';
    $cleanName = preg_replace('/\s*\([^)]*\)/', '', $rawName);
    $userName  = !empty(trim($cleanName)) ? trim($cleanName) : $rawName;

    $words = explode(' ', trim($userName));
    if (count($words) >= 2) {
        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    } else {
        $initials = strtoupper(substr($userName, 0, 2));
    }

    $roleTitle = match($role) {
        'admin'  => 'System Admin',
        'gudang' => 'Gudang Master',
        'kasir'  => 'Kasir POS',
        default  => ucfirst($role ?? 'User'),
    };
@endphp

<aside class="dt-sidebar">
    {{-- Brand --}}
    <div class="dt-sidebar-brand d-flex align-items-center justify-content-between" style="padding:18px 20px;border-bottom:1px solid rgba(255,255,255,.08);">
        <a href="#" class="d-flex align-items-center gap-2" style="text-decoration:none">
            <img src="{{ asset('images/logo_kainkita_transparent.png') }}" alt="KainKita Icon" style="height:38px; width:auto; object-fit:contain; flex-shrink:0;">
            <div>
                <span style="font-size:21px;font-weight:800;color:var(--dt-white);letter-spacing:.5px;display:block;line-height:1.1">
                    {!! $formattedStoreNameLight !!}
                </span>
                <span style="font-size:9.5px;color:rgba(255,255,255,.5);letter-spacing:.3px;display:block;margin-top:2px;white-space:nowrap">
                    Sistem Penerimaan & Distribusi Kain
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
                <a href="{{ route('admin.incoming-goods.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.incoming-goods*') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-in-down"></i> Riwayat Barang Masuk
                </a>
                <a href="{{ route('admin.delivery-orders.index') }}" class="dt-nav-sublink {{ request()->routeIs('admin.delivery-orders*') ? 'active' : '' }} d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-truck"></i> Surat Jalan Masuk</span>
                    @php
                        $pendingAdminSjCount = \App\Models\DeliveryOrder::where('status', 'menunggu_approval')->count();
                    @endphp
                    @if($pendingAdminSjCount > 0)
                        <span class="badge rounded-pill bg-primary text-white px-2 py-0.5" style="font-size: 10px;">{{ $pendingAdminSjCount }}</span>
                    @endif
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
                <a href="{{ route('gudang.delivery-orders.index') }}" class="dt-nav-sublink {{ request()->routeIs('gudang.delivery-orders*') ? 'active' : '' }} d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-box-arrow-in-down-right"></i> Surat Jalan Masuk</span>
                    @php
                        $pendingGudangSjCount = \App\Models\DeliveryOrder::whereIn('status', ['disetujui_admin', 'dikirim'])->count();
                    @endphp
                    @if($pendingGudangSjCount > 0)
                        <span class="badge rounded-pill bg-primary text-white px-2 py-0.5" style="font-size: 10px;">{{ $pendingGudangSjCount }}</span>
                    @endif
                </a>
                <a href="{{ route('gudang.incoming-goods.create') }}" class="dt-nav-sublink {{ request()->routeIs('gudang.incoming-goods.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i> Input Barang Masuk
                </a>
                <a href="{{ route('gudang.incoming-goods.index') }}" class="dt-nav-sublink {{ request()->routeIs('gudang.incoming-goods.index') && !request()->routeIs('gudang.incoming-goods.create') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-in-down"></i> Riwayat Barang Masuk
                </a>
            </div>
        </div>

        @elseif($role === 'kasir')
        {{-- ======== KASIR MENU ======== --}}
        <div class="dt-nav-section">Layanan Kasir</div>
        <a href="{{ route('kasir.sales.pos') }}" class="dt-nav-link {{ request()->routeIs('kasir.sales*') ? 'active' : '' }}">
            <i class="bi bi-cart3"></i> POS Penjualan
        </a>

        <a href="{{ route('kasir.transactions.index') }}" class="dt-nav-link {{ request()->routeIs('kasir.transactions*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i> Transaksi Hari Ini
        </a>

        <a href="{{ route('kasir.income.index') }}" class="dt-nav-link {{ request()->routeIs('kasir.income*') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i> Rekap Pendapatan
        </a>
        @endif

    </nav>

    {{-- Footer --}}
    <div class="dt-sidebar-footer border-top" style="border-color: rgba(255, 255, 255, 0.08) !important; background: #0f172a; padding: 12px 10px; box-sizing: border-box; width: 100%;">
        <div class="d-flex align-items-center justify-content-between rounded-3 w-100" style="background: #1e293b; border: 1px solid rgba(255, 255, 255, 0.08); padding: 8px 10px; gap: 8px; box-sizing: border-box; overflow: hidden;">
            <div class="d-flex align-items-center" style="gap: 8px; min-width: 0; flex: 1; overflow: hidden;">
                <div class="d-flex align-items-center justify-content-center rounded-circle text-white flex-shrink-0" style="width: 32px; height: 32px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); font-size: 12px; font-weight: 700; letter-spacing: 0.3px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    {{ $initials }}
                </div>
                <div style="min-width: 0; flex: 1; overflow: hidden;">
                    <div class="text-truncate text-white fw-semibold" style="font-size: 12px; line-height: 1.25;" title="{{ $rawName }}">{{ $userName }}</div>
                    <div class="text-truncate" style="font-size: 10.5px; color: #94a3b8; margin-top: 2px;">{{ $roleTitle }}</div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="m-0 flex-shrink-0">
                @csrf
                <button type="submit" class="btn btn-link text-decoration-none border-0 d-flex align-items-center justify-content-center rounded-2 p-0" style="color: #94a3b8; transition: all 0.2s; width: 28px; height: 28px; background: rgba(255, 255, 255, 0.05);" title="Logout / Keluar" onmouseover="this.style.color='#f87171'; this.style.background='rgba(239, 68, 68, 0.2)';" onmouseout="this.style.color='#94a3b8'; this.style.background='rgba(255, 255, 255, 0.05)';">
                    <i class="bi bi-box-arrow-right" style="font-size: 14px;"></i>
                </button>
            </form>
        </div>
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
