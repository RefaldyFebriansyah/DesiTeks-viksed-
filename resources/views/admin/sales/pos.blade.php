@extends('layouts.app')
@section('title', 'POS - Penjualan')
@section('page-title', 'Penjualan (POS)')

@push('styles')
<style>
.dt-pos-wrap {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 20px;
    height: calc(100vh - 120px);
    overflow: hidden;
}
.pos-left {
    overflow-y: auto;
    padding-right: 8px;
    display: flex;
    flex-direction: column;
}
.pos-right {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--dt-white);
    border-radius: 14px;
    border: 1px solid var(--dt-border);
    box-shadow: var(--dt-shadow-md);
}

/* Mobile Tab Switcher Styles */
.dt-pos-mobile-tabs {
    background: #f1f5f9;
    padding: 4px;
    border-radius: 10px;
    border: 1px solid var(--dt-border);
    display: flex;
    gap: 4px;
}
.dt-pos-tab-btn {
    background: transparent;
    border: none;
    color: var(--dt-muted);
    font-size: 13.5px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}
.dt-pos-tab-btn.active {
    background: var(--dt-navy);
    color: var(--dt-white) !important;
    box-shadow: var(--dt-shadow-sm);
}

@keyframes pulseBadge {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}
.pulse-animation {
    animation: pulseBadge 0.3s ease-in-out;
}

@media (max-width: 991px) {
    .dt-pos-wrap {
        grid-template-columns: 1fr;
        height: calc(100vh - 160px);
        overflow: hidden;
        gap: 0;
    }
    .pos-left {
        overflow-y: auto;
        padding-right: 0;
        height: 100%;
        display: block !important;
    }
    .pos-right {
        overflow: hidden;
        height: 100%;
        margin-top: 0;
        display: none !important;
    }
    .dt-cart {
        height: 100%;
    }
    .dt-cart-body {
        flex: 1;
        overflow-y: auto;
    }
}
.fabric-search {
    position: sticky;
    top: 0;
    background: var(--dt-bg);
    padding-bottom: 12px;
    z-index: 10;
}

/* Category Pills */
.dt-pill {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background: var(--dt-white);
    border: 1.5px solid var(--dt-border);
    border-radius: 20px;
    color: var(--dt-text);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    cursor: pointer;
}
.dt-pill:hover {
    border-color: var(--dt-navy);
    color: var(--dt-navy);
    transform: translateY(-1px);
}
.dt-pill.active {
    background: var(--dt-navy);
    border-color: var(--dt-navy);
    color: var(--dt-white);
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(15,39,68,0.2);
}

/* Fabric Cards Grid */
.dt-fabric-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    padding-bottom: 20px;
}
.dt-fabric-item {
    background: var(--dt-white);
    border: 1.5px solid var(--dt-border);
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}
.dt-fabric-item:hover {
    border-color: var(--dt-navy);
    box-shadow: 0 8px 24px rgba(15, 39, 68, 0.12);
    transform: translateY(-4px);
}
.dt-fabric-item.out-of-stock {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
    background: #f8fafc;
}
.fabric-card-code {
    font-size: 11px;
    color: var(--dt-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.color-swatch-badge {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: inline-block;
    box-shadow: inset 0 0 2px rgba(0,0,0,0.2);
}
.fabric-card-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--dt-navy);
    margin: 4px 0 2px;
    line-height: 1.3;
}
.fabric-card-meta {
    font-size: 12px;
    color: var(--dt-muted);
}
.price-label {
    font-size: 11.5px;
    color: var(--dt-muted);
}
.bg-navy-light {
    background-color: rgba(15,39,68,0.1);
    color: var(--dt-navy);
}
.bg-gold-light {
    background-color: rgba(201,168,76,0.15);
    color: #8a6d20;
}

/* Cart Panel Styling */
.dt-cart {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    background: transparent;
    border: none;
    border-radius: 0;
}
.dt-cart-header {
    padding: 18px 20px;
    background: var(--dt-navy);
    color: var(--dt-white);
    font-weight: 600;
    font-size: 15px;
    border-bottom: 2px solid var(--dt-gold);
}
.dt-cart-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}
.dt-cart-item {
    background: #f8fafc;
    border: 1px solid var(--dt-border);
    border-radius: 10px;
    padding: 8px 10px;
    margin-bottom: 8px;
    font-size: 13px;
    transition: all 0.2s ease;
}
.dt-cart-item:hover {
    border-color: var(--dt-gold);
    background: var(--dt-white);
    box-shadow: 0 4px 12px rgba(15,39,68,0.05);
}
.dt-cart-footer {
    padding: 12px 16px;
    border-top: 1px solid var(--dt-border);
    background: var(--dt-white);
}
.dt-cart-total {
    font-size: 22px;
    font-weight: 800;
    color: var(--dt-navy);
}

/* Stepper Quantity & Unit Toggles */
.dt-unit-group {
    display: inline-flex;
    border-radius: 6px;
    overflow: hidden;
    border: 1.5px solid var(--dt-border);
}
.dt-unit-btn {
    padding: 4px 10px;
    font-size: 11.5px;
    font-weight: 600;
    background: var(--dt-white);
    color: var(--dt-muted);
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    outline: none;
}
.dt-unit-btn.active {
    background: var(--dt-navy);
    color: var(--dt-white);
}
.dt-unit-btn:not(:last-child) {
    border-right: 1.5px solid var(--dt-border);
}
.dt-stepper {
    display: inline-flex;
    align-items: center;
    border: 1.5px solid var(--dt-border);
    border-radius: 6px;
    background: var(--dt-white);
    overflow: hidden;
}
.dt-stepper-btn {
    border: none;
    background: var(--dt-white);
    color: var(--dt-navy);
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.15s;
}
.dt-stepper-btn:hover {
    background: var(--dt-bg);
}
.dt-stepper-input {
    border: none;
    border-left: 1px solid var(--dt-border);
    border-right: 1px solid var(--dt-border);
    width: 54px;
    height: 28px;
    text-align: center;
    font-size: 12.5px;
    font-weight: 600;
    outline: none;
}

/* Payment visual cards */
.payment-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
}
.payment-card {
    border: 1.5px solid var(--dt-border);
    border-radius: 6px;
    padding: 5px 2px;
    text-align: center;
    cursor: pointer;
    background: var(--dt-white);
    transition: all 0.15s ease;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.payment-card i {
    font-size: 13px;
    color: var(--dt-muted);
}
.payment-card span {
    font-size: 10.5px;
    font-weight: 600;
    color: var(--dt-text);
}
.payment-card:hover {
    border-color: var(--dt-navy);
    background: #f8fafc;
}
.payment-card.active {
    border-color: var(--dt-gold);
    background: rgba(201, 168, 76, 0.08);
    box-shadow: 0 2px 6px rgba(201, 168, 76, 0.15);
}
.payment-card.active i {
    color: var(--dt-gold);
}
.payment-card.active span {
    color: #8a6d20;
}

/* Quick Pay Buttons */
.quick-pay-wrap {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
}
.quick-pay-btn {
    border: 1px solid var(--dt-border);
    border-radius: 6px;
    background: var(--dt-white);
    padding: 6px 2px;
    font-size: 11px;
    font-weight: 600;
    color: var(--dt-navy);
    cursor: pointer;
    transition: all 0.15s ease;
    text-align: center;
}
.quick-pay-btn:hover {
    background: var(--dt-navy);
    color: var(--dt-white);
    border-color: var(--dt-navy);
}
</style>
@endpush

@section('content')
@php $role = auth()->user()->role; $storeRoute = $role==='admin' ? 'admin.sales.store' : 'kasir.sales.store'; @endphp

{{-- Mobile Tab Switcher --}}
<div class="dt-pos-mobile-tabs d-lg-none mb-3">
    <button type="button" id="tabFabrics" class="dt-pos-tab-btn active flex-fill py-2 rounded" onclick="switchMobileTab('fabrics')">
        <i class="bi bi-grid-3x3-gap me-1.5"></i> Pilih Kain
    </button>
    <button type="button" id="tabCart" class="dt-pos-tab-btn flex-fill py-2 rounded position-relative" onclick="switchMobileTab('cart')">
        <i class="bi bi-cart-fill me-1.5"></i> Keranjang
        <span class="badge bg-danger text-white ms-1 rounded-pill" id="mobileCartBadge" style="font-size:10px; padding: 2px 5px;">0</span>
    </button>
</div>

<div class="dt-pos-wrap">
    {{-- ═══ LEFT: Daftar Kain ═══ --}}
    <div class="pos-left">
        <div class="fabric-search">
            <form method="GET" class="d-flex gap-2 mb-3">
                <div class="position-relative" style="flex: 1;">
                    <i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 14px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="dt-input" placeholder="Scan barcode atau cari berdasarkan nama/kode kain..." style="padding-left: 36px;" autocomplete="off">
                </div>
                @if(request()->hasAny(['search','category_id']))
                    <a href="{{ url()->current() }}" class="dt-btn dt-btn-outline"><i class="bi bi-x-lg"></i> Reset</a>
                @endif
                <button class="dt-btn dt-btn-primary px-4"><i class="bi bi-search"></i> Cari</button>
            </form>

            {{-- Category Pills --}}
            @php
                $currentCatId = request('category_id');
            @endphp
            <div class="d-flex gap-2 overflow-x-auto pb-2" style="white-space: nowrap; -webkit-overflow-scrolling: touch; scrollbar-width: none;">
                <a href="{{ url()->current() }}{{ request('search') ? '?search='.request('search') : '' }}" 
                   class="dt-pill {{ !$currentCatId ? 'active' : '' }}">
                    <i class="bi bi-grid-fill me-1.5"></i> Semua Kategori
                </a>
                @foreach($categories as $cat)
                    <a href="{{ url()->current() }}?category_id={{ $cat->id }}{{ request('search') ? '&search='.request('search') : '' }}" 
                       class="dt-pill {{ $currentCatId == $cat->id ? 'active' : '' }}">
                        {{ $cat->nama_kategori }}
                    </a>
                @endforeach
            </div>
        </div>

        @if(session('error'))
            <div class="dt-alert dt-alert-danger mb-3"><i class="bi bi-exclamation-triangle-fill"></i><span>{{ session('error') }}</span></div>
        @endif

        @if($fabrics->isEmpty())
            <div class="empty-state"><i class="bi bi-search fs-3"></i><h5>Kain tidak ditemukan</h5></div>
        @else
        <div class="dt-fabric-grid">
            @foreach($fabrics as $fabric)
            @php
                $stok = $fabric->stock;
                $habis = !$stok || ($stok->stok_meter <= 0 && $stok->stok_rol <= 0);
                
                // Color swatch mapping
                $colorHex = '#ccc';
                $colorName = strtolower(trim($fabric->warna));
                $colorMap = [
                    'merah' => '#ef4444',
                    'biru' => '#3b82f6',
                    'hijau' => '#10b981',
                    'kuning' => '#eab308',
                    'hitam' => '#1e293b',
                    'putih' => '#f8fafc',
                    'abu' => '#94a3b8',
                    'abu-abu' => '#94a3b8',
                    'cokelat' => '#78350f',
                    'coklat' => '#78350f',
                    'pink' => '#ec4899',
                    'merah muda' => '#ec4899',
                    'ungu' => '#a855f7',
                    'navy' => '#0f2744',
                    'gold' => '#c9a84c',
                    'orange' => '#f97316',
                    'oranye' => '#f97316',
                ];
                foreach($colorMap as $key => $hex) {
                    if(strpos($colorName, $key) !== false) {
                        $colorHex = $hex;
                        break;
                    }
                }
            @endphp
            <div class="dt-fabric-item {{ $habis ? 'out-of-stock' : '' }}" onclick="addToCart({{ $fabric->id }}, '{{ addslashes($fabric->nama_kain) }}', {{ $fabric->harga_per_meter }}, {{ $fabric->harga_per_rol }}, {{ $stok?->stok_meter ?? 0 }}, {{ $stok?->stok_rol ?? 0 }}, {{ $fabric->meter_per_rol }})">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fabric-card-code">{{ $fabric->kode_kain }}</span>
                        <span class="color-swatch-badge" style="background: {{ $colorHex }}; border: 1px solid rgba(0,0,0,0.15);" title="Warna: {{ $fabric->warna }}"></span>
                    </div>
                    <div class="fabric-card-title">{{ $fabric->nama_kain }}</div>
                    <div class="fabric-card-meta">
                        <span><i class="bi bi-tag-fill me-1 text-navy" style="opacity: 0.6"></i>{{ $fabric->category->nama_kategori }}</span>
                        @if($fabric->motif && strtolower($fabric->motif) !== 'polos')
                            <span class="ms-2"><i class="bi bi-palette2 me-1 text-gold"></i>{{ $fabric->motif }}</span>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="fabric-card-pricing py-2 my-2 border-top border-bottom border-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price-label">Meter:</span>
                            <span class="price-value fw-700 text-navy">Rp {{ number_format($fabric->harga_per_meter,0,',','.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price-label">Rol:</span>
                            <span class="price-value text-muted" style="font-size: 11px">Rp {{ number_format($fabric->harga_per_rol,0,',','.') }}</span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        @if($habis)
                            <span class="badge bg-danger px-2.5 py-1">Habis</span>
                        @else
                            @php
                                $totalMeters = ($stok?->stok_meter ?? 0) + (($stok?->stok_rol ?? 0) * $fabric->meter_per_rol);
                            @endphp
                            <div class="d-flex gap-1">
                                <span class="badge bg-navy-light text-navy px-2 py-1" style="font-size: 10px;" title="Sisa meteran eceran: {{ number_format($stok?->stok_meter ?? 0, 1) }} m">
                                    <i class="bi bi-ruler me-0.5"></i> {{ number_format($totalMeters, 1) }} m
                                </span>
                                <span class="badge bg-gold-light text-navy px-2 py-1" style="font-size: 10px; font-weight: 600;">
                                    <i class="bi bi-box-seam me-0.5"></i> {{ $stok?->stok_rol ?? 0 }} r
                                </span>
                            </div>
                            @if($stok && $totalMeters <= $fabric->stok_minimum)
                                <span class="badge bg-warning text-white px-2 py-1" style="font-size: 10px;">Menipis</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ═══ RIGHT: Keranjang & Checkout ═══ --}}
    <div class="pos-right">
        <div class="dt-cart">
            <div class="dt-cart-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cart-fill me-2 text-gold"></i>Keranjang</span>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-xs btn-outline-light text-white border-white-50" data-bs-toggle="modal" data-bs-target="#kalkulatorKainModal" style="font-size: 11px; padding: 2px 8px; background: rgba(255,255,255,0.15)">
                        <i class="bi bi-calculator me-1"></i> Kalkulator Kain
                    </button>
                    <span class="badge bg-warning text-navy px-2.5 py-1 fw-700" id="cartCount" style="font-size: 12px;">0 item</span>
                </div>
            </div>

            <div class="dt-cart-body" id="cartBody">
                <div class="dt-cart-empty text-center py-5" id="cartEmpty">
                    <i class="bi bi-basket text-muted" style="font-size:48px; opacity:.4"></i>
                    <p class="mt-2 text-muted" style="font-size:13.5px">Keranjang masih kosong.<br>Silakan pilih kain atau scan barcode.</p>
                </div>
                <div id="cartItems"></div>
            </div>

            {{-- Pinned Footer containing Checkout Form --}}
            <div class="dt-cart-footer d-none" id="cartFooter">
                <div id="checkoutFormWrapper">
                    {{-- Pricing Summary Box --}}
                    <div class="pricing-summary py-2 px-3 rounded mb-2" style="background:#f8fafc; border:1px solid var(--dt-border); font-size:12.5px;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Subtotal:</span>
                            <span id="posSubtotal" class="fw-600 text-navy">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between text-success d-none mb-1" id="posDiscountRow">
                            <span><i class="bi bi-tag-fill me-1"></i> Diskon Member:</span>
                            <span id="posDiscount" class="fw-600">-Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between pt-1 border-top border-light total-row fw-700 text-navy">
                            <span>Total Bayar:</span>
                            <span id="cartTotal" style="font-size:15px;">Rp 0</span>
                        </div>
                    </div>

                    <form id="saleForm" method="POST" action="{{ route($storeRoute) }}" onsubmit="return validateSale()">
                        @csrf
                        <div id="cartInputs"></div>
                        <input type="hidden" name="diskon" id="inputDiskon" value="0">
                        <input type="hidden" name="pajak" id="inputPajak" value="0">

                        {{-- Hidden select for fallback, visually updated by custom cards --}}
                        <select name="metode" id="metode" class="d-none">
                            <option value="tunai" selected>Tunai</option>
                            <option value="transfer">Transfer</option>
                            <option value="qris">QRIS</option>
                        </select>

                        {{-- Pelanggan Inline --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="dt-label m-0" style="font-size:12px; font-weight: 600; color:var(--dt-muted)">Pelanggan:</label>
                            <select name="customer_id" id="customer_id" class="dt-select py-1 px-2" style="width: 70%; font-size:12px; height: 30px;" onchange="applyCustomerDiscount()">
                                <option value="">-- Pelanggan Eceran (Umum) --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" data-tipe="{{ $c->tipe }}" data-diskon="{{ $c->diskon_member }}">
                                        {{ $c->nama }} ({{ ucfirst($c->tipe) }}{{ $c->tipe === 'member' ? ' - ' . number_format($c->diskon_member, 1) . '%' : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Metode Pembayaran Inline --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="dt-label m-0" style="font-size:12px; font-weight: 600; color:var(--dt-muted)">Metode:</label>
                            <div class="payment-grid" style="width: 70%;">
                                <div class="payment-card active" onclick="selectPayment('tunai')">
                                    <i class="bi bi-cash-coin"></i>
                                    <span>Tunai</span>
                                </div>
                                <div class="payment-card" onclick="selectPayment('transfer')">
                                    <i class="bi bi-bank"></i>
                                    <span>Transf</span>
                                </div>
                                <div class="payment-card" onclick="selectPayment('qris')">
                                    <i class="bi bi-qr-code-scan"></i>
                                    <span>QRIS</span>
                                </div>
                            </div>
                        </div>

                        {{-- Bayar Inline --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex flex-column align-items-start">
                                <label class="dt-label m-0" style="font-size:12px; font-weight: 600; color:var(--dt-muted)">Bayar:</label>
                                <span id="quickPayLabel" style="font-size:10px; color:var(--dt-gold); cursor:pointer; text-decoration: underline;" onclick="setExactAmount()">Uang Pas</span>
                            </div>
                            <div class="position-relative" style="width: 70%;">
                                <span class="position-absolute fw-700 text-navy" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 13px;">Rp</span>
                                <input type="number" id="jumlahBayar" name="jumlah_bayar" class="dt-input py-1 ps-4 pe-2" style="font-size:14px; font-weight:700; height:32px;" placeholder="0" min="0" step="1" oninput="hitungKembalian()">
                            </div>
                        </div>

                        {{-- Quick pay options --}}
                        <div class="d-flex justify-content-end gap-1 mb-2" id="quickPayWrapper">
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 10px; border-radius: 4px; height: 20px; color: var(--dt-navy); border-color: var(--dt-border);" onclick="addPay(10000)">+10k</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 10px; border-radius: 4px; height: 20px; color: var(--dt-navy); border-color: var(--dt-border);" onclick="addPay(50000)">+50k</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 10px; border-radius: 4px; height: 20px; color: var(--dt-navy); border-color: var(--dt-border);" onclick="addPay(100000)">+100k</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 10px; border-radius: 4px; height: 20px; color: var(--dt-navy); border-color: var(--dt-border);" onclick="addPay(200000)">+200k</button>
                        </div>

                        {{-- Kembalian --}}
                        <div class="d-flex justify-content-between align-items-center mb-2 pt-1 border-top border-light">
                            <span style="font-size:12px; color:var(--dt-muted)">Kembalian:</span>
                            <span id="kembalian" class="fw-800" style="font-size:15px; color:var(--dt-muted)">Rp 0</span>
                        </div>

                        <button type="submit" id="btnBayar" class="dt-btn dt-btn-gold w-100 justify-content-center disabled" style="padding:8px; font-size:13.5px; border-radius: 6px; font-weight:600;" disabled>
                            <i class="bi bi-check-circle-fill me-2"></i> Selesaikan Transaksi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Kalkulator Kebutuhan Kain --}}
<div class="modal fade" id="kalkulatorKainModal" tabindex="-1" aria-labelledby="kalkulatorKainModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--dt-navy); border-bottom: 2px solid var(--dt-gold)">
                <h5 class="modal-title" id="kalkulatorKainModalLabel"><i class="bi bi-calculator me-2 text-gold"></i>Kalkulator Kebutuhan Kain</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="dt-label">Jenis Pakaian</label>
                    <select id="calcJenis" class="dt-select">
                        <option value="1.5">👕 Kemeja Lengan Pendek (1.5 m)</option>
                        <option value="2.0" selected>👔 Kemeja Lengan Panjang (2.0 m)</option>
                        <option value="3.0">👗 Gamis Dewasa / Dress (3.0 m)</option>
                        <option value="1.5">👖 Celana Panjang (1.5 m)</option>
                        <option value="2.0">🧥 Rok Panjang (2.0 m)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="dt-label">Ukuran Badan</label>
                    <select id="calcUkuran" class="dt-select">
                        <option value="0.9">S (Kecil - x0.9)</option>
                        <option value="1.0" selected>M (Standar - x1.0)</option>
                        <option value="1.1">L (Sedang - x1.1)</option>
                        <option value="1.2">XL (Besar - x1.2)</option>
                        <option value="1.3">XXL (Ekstra Besar - x1.3)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="dt-label">Jumlah Potong Pakaian</label>
                    <input type="number" id="calcJumlah" class="dt-input" value="1" min="1" step="1">
                </div>
                
                <div class="p-3 rounded mb-3 text-center" style="background: #f1f5f9; border: 1px solid var(--dt-border)">
                    <div class="text-muted" style="font-size: 12px; font-weight: 500">Estimasi Kebutuhan Kain:</div>
                    <div class="fw-800 text-navy mt-1" style="font-size: 24px;"><span id="calcResult">2.0</span> <span style="font-size: 16px; font-weight: 500">meter</span></div>
                </div>

                <div class="mb-3" id="calcApplyToWrapper" style="display: none;">
                    <label class="dt-label">Terapkan Langsung ke Keranjang</label>
                    <select id="calcApplyTo" class="dt-select"></select>
                </div>

                <div class="alert alert-warning py-2 px-3 border-0 d-flex gap-2 align-items-center mb-0" style="font-size: 11px; background: var(--dt-warning-bg); color: #874b12; border-radius: 8px;">
                    <i class="bi bi-info-circle-fill fs-6 flex-shrink-0"></i>
                    <span>Hasil ini adalah perkiraan umum. Silakan konsultasikan dengan penjahit Anda untuk kebutuhan tepat.</span>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="dt-btn dt-btn-outline" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="dt-btn dt-btn-gold" onclick="applyCalculatorResult()"><i class="bi bi-check-lg me-1"></i> Terapkan ke Kuantitas</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Local fabric codes lookup dictionary for instant barcode scan auto-add
    const fabricLookup = {
        @foreach($fabrics as $fabric)
            "{{ strtolower(trim($fabric->kode_kain)) }}": {
                id: {{ $fabric->id }},
                nama: "{{ addslashes($fabric->nama_kain) }}",
                hargaMeter: {{ $fabric->harga_per_meter }},
                hargaRol: {{ $fabric->harga_per_rol }},
                stokMeter: {{ $fabric->stock?->stok_meter ?? 0 }},
                stokRol: {{ $fabric->stock?->stok_rol ?? 0 }}
            },
        @endforeach
    };

    // Intercept barcode scans or manual fabric codes entered in the search bar
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.querySelector('.fabric-search form');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                const searchInput = this.querySelector('input[name="search"]');
                const query = searchInput.value.trim().toLowerCase();
                
                if (fabricLookup && fabricLookup[query]) {
                    e.preventDefault(); // Mencegah submit form reload halaman
                    const fabric = fabricLookup[query];
                    addToCart(fabric.id, fabric.nama, fabric.hargaMeter, fabric.hargaRol, fabric.stokMeter, fabric.stokRol);
                    searchInput.value = ''; // Kosongkan input kembali
                    
                    // Show a toast or feedback if necessary
                    console.log('Barcode/Kode terdeteksi: ' + fabric.nama + ' ditambahkan ke keranjang.');
                }
            });
        }
    });

    // Set payment function to hook with the redesigned custom card elements
    function selectPayment(metode) {
        document.getElementById('metode').value = metode;
        document.querySelectorAll('.payment-card').forEach(card => {
            card.classList.remove('active');
        });
        
        // Find click target and make active
        if (metode === 'tunai') {
            document.querySelector('.payment-card:nth-child(1)').classList.add('active');
            document.getElementById('quickPayWrapper').style.display = 'block';
            document.getElementById('quickPayLabel').style.display = 'inline';
        } else if (metode === 'transfer') {
            document.querySelector('.payment-card:nth-child(2)').classList.add('active');
            document.getElementById('quickPayWrapper').style.display = 'none';
            document.getElementById('quickPayLabel').style.display = 'none';
            // Auto fill full payment amount for digital
            setExactAmount();
        } else if (metode === 'qris') {
            document.querySelector('.payment-card:nth-child(3)').classList.add('active');
            document.getElementById('quickPayWrapper').style.display = 'none';
            document.getElementById('quickPayLabel').style.display = 'none';
            // Auto fill full payment amount for digital
            setExactAmount();
        }
    }
    
    function setExactAmount() {
        const total = getTotalCart();
        document.getElementById('jumlahBayar').value = total;
        hitungKembalian();
    }

    function addPay(amt) {
        const input = document.getElementById('jumlahBayar');
        let current = parseFloat(input.value) || 0;
        input.value = current + amt;
        hitungKembalian();
    }

    function applyCustomerDiscount() {
        if (typeof renderCart === 'function') {
            renderCart();
        }
    }

    function switchMobileTab(tab) {
        const fabricsTab = document.getElementById('tabFabrics');
        const cartTab = document.getElementById('tabCart');
        const posLeft = document.querySelector('.pos-left');
        const posRight = document.querySelector('.pos-right');
        
        if (!fabricsTab || !cartTab || !posLeft || !posRight) return;
        
        if (tab === 'fabrics') {
            fabricsTab.classList.add('active');
            cartTab.classList.remove('active');
            posLeft.style.setProperty('display', 'block', 'important');
            posRight.style.setProperty('display', 'none', 'important');
        } else {
            cartTab.classList.add('active');
            fabricsTab.classList.remove('active');
            posLeft.style.setProperty('display', 'none', 'important');
            posRight.style.setProperty('display', 'block', 'important');
        }
    }
</script>
<script src="{{ asset('js/pos.js') }}"></script>
@endpush
