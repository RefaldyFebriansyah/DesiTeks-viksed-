@php $role = auth()->user()->role; @endphp

{{-- Overlay for mobile --}}
<div class="d-lg-none" id="sidebarOverlay" style="display:none!important;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:99"
     onclick="document.querySelector('.dt-sidebar').classList.remove('show');this.style.display='none'"></div>

<aside class="dt-sidebar">
    {{-- Brand --}}
    <a href="#" class="dt-sidebar-brand d-flex align-items-center gap-2" style="padding:18px 20px;border-bottom:1px solid rgba(255,255,255,.08);text-decoration:none">
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


    <nav class="dt-nav">

        @if($role === 'admin')
        {{-- ======== ADMIN MENU ======== --}}
        <div class="dt-nav-section">Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="dt-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="dt-nav-section">Data Master</div>
        <a href="{{ route('admin.fabrics.index') }}" class="dt-nav-link {{ request()->routeIs('admin.fabrics*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> Data Kain
        </a>
        <a href="{{ route('admin.categories.index') }}" class="dt-nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Kategori Kain
        </a>
        <a href="{{ route('admin.suppliers.index') }}" class="dt-nav-link {{ request()->routeIs('admin.suppliers*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Supplier
        </a>

        <div class="dt-nav-section">Gudang</div>
        <a href="{{ route('admin.stocks.index') }}" class="dt-nav-link {{ request()->routeIs('admin.stocks*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Stok
        </a>
        <a href="{{ route('admin.incoming-goods.index') }}" class="dt-nav-link {{ request()->routeIs('admin.incoming-goods*') ? 'active' : '' }}">
            <i class="bi bi-arrow-down-square"></i> Barang Masuk
        </a>
        <a href="{{ route('admin.stock-movements.index') }}" class="dt-nav-link {{ request()->routeIs('admin.stock-movements*') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right"></i> Riwayat Stok
        </a>

        <div class="dt-nav-section">Penjualan</div>
        <a href="{{ route('admin.sales.pos') }}" class="dt-nav-link {{ request()->routeIs('admin.sales*') ? 'active' : '' }}">
            <i class="bi bi-cart3"></i> Penjualan (POS)
        </a>
        <a href="{{ route('admin.transactions.index') }}" class="dt-nav-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Transaksi
        </a>
        <a href="{{ route('admin.reports.index') }}" class="dt-nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Laporan
        </a>

        <div class="dt-nav-section">Sistem</div>
        <a href="{{ route('admin.users.index') }}" class="dt-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Pengguna
        </a>
        <a href="{{ route('admin.audit-logs.index') }}" class="dt-nav-link {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Aktivitas Sistem
        </a>

        @elseif($role === 'gudang')
        {{-- ======== GUDANG MENU ======== --}}
        <div class="dt-nav-section">Utama</div>
        <a href="{{ route('gudang.dashboard') }}" class="dt-nav-link {{ request()->routeIs('gudang.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="dt-nav-section">Stok & Supplier</div>
        <a href="{{ route('gudang.stocks.index') }}" class="dt-nav-link {{ request()->routeIs('gudang.stocks*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Stok Kain
        </a>
        <a href="{{ route('gudang.suppliers.index') }}" class="dt-nav-link {{ request()->routeIs('gudang.suppliers*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Supplier
        </a>
        <a href="{{ route('gudang.incoming-goods.index') }}" class="dt-nav-link {{ request()->routeIs('gudang.incoming-goods*') ? 'active' : '' }}">
            <i class="bi bi-arrow-down-square"></i> Barang Masuk
        </a>

        @elseif($role === 'kasir')
        {{-- ======== KASIR MENU ======== --}}
        <div class="dt-nav-section">Utama</div>
        <a href="{{ route('kasir.dashboard') }}" class="dt-nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="dt-nav-section">Transaksi</div>
        <a href="{{ route('kasir.sales.pos') }}" class="dt-nav-link {{ request()->routeIs('kasir.sales*') ? 'active' : '' }}">
            <i class="bi bi-cart3"></i> Penjualan (POS)
        </a>
        <a href="{{ route('kasir.transactions.index') }}" class="dt-nav-link {{ request()->routeIs('kasir.transactions*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Transaksi Hari Ini
        </a>
        <a href="{{ route('kasir.income.index') }}" class="dt-nav-link {{ request()->routeIs('kasir.income*') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i> Pendapatan Hari Ini
        </a>

        <div class="dt-nav-section">Info</div>
        <a href="{{ route('kasir.stocks.index') }}" class="dt-nav-link {{ request()->routeIs('kasir.stocks*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Cek Stok
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
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dt-nav-link w-100 border-0" style="background:none;color:rgba(255,100,100,.7);text-align:left;cursor:pointer">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</aside>
