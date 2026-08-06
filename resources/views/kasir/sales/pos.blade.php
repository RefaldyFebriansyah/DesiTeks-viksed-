{{-- Kasir POS - extends same template with kasir route --}}
@extends('layouts.app')
@section('title', 'POS - Penjualan')
@section('page-title', 'Penjualan (POS)')

@push('styles')
<style>
.dt-pos-wrap { display:grid; grid-template-columns:1fr 390px; gap:16px; height:calc(100vh - 108px); overflow:hidden; }
.pos-left { overflow-y:auto; padding-right:4px; }
.pos-right { display:flex; flex-direction:column; overflow:hidden; }
.fabric-search { position:sticky; top:0; background:var(--dt-bg); padding-bottom:12px; z-index:10; }
</style>
@endpush

@section('content')
@php $role = 'kasir'; $storeRoute = 'kasir.sales.store'; @endphp

<div class="dt-pos-wrap">
    <div class="pos-left">
        <div class="fabric-search">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" class="dt-input" placeholder="Cari kain..." style="flex:1">
                <select name="category_id" class="dt-select" style="width:160px">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
                <button class="dt-btn dt-btn-primary"><i class="bi bi-search"></i></button>
                @if(request()->hasAny(['search','category_id']))
                    <a href="{{ url()->current() }}" class="dt-btn dt-btn-outline"><i class="bi bi-x"></i></a>
                @endif
            </form>
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
            @endphp
            <div class="dt-fabric-item {{ $habis ? 'out-of-stock' : '' }}" onclick="addToCart({{ $fabric->id }}, '{{ addslashes($fabric->nama_kain) }}', {{ $fabric->harga_per_meter }}, {{ $fabric->harga_per_rol }}, {{ $stok?->stok_meter ?? 0 }}, {{ $stok?->stok_rol ?? 0 }})">
                <div class="dt-fabric-name">{{ $fabric->nama_kain }}</div>
                <div class="dt-fabric-code">{{ $fabric->kode_kain }} · {{ $fabric->category->nama_kategori }}</div>
                <div class="dt-fabric-price mt-2">
                    <div><span class="fw-600">Rp {{ number_format($fabric->harga_per_meter,0,',','.') }}</span><span style="font-size:11px;color:var(--dt-muted)">/meter</span></div>
                    <div><span style="color:var(--dt-muted);font-size:12px">Rp {{ number_format($fabric->harga_per_rol,0,',','.') }}/rol</span></div>
                </div>
                <div class="dt-fabric-stock mt-2 d-flex gap-2">
                    @if($habis)
                        <span class="dt-badge dt-badge-danger">Habis</span>
                    @elseif($stok && $stok->stok_meter <= $fabric->stok_minimum)
                        <span class="dt-badge dt-badge-warning">{{ number_format($stok->stok_meter,1) }} m</span>
                    @else
                        <span class="dt-badge dt-badge-navy">{{ number_format($stok?->stok_meter ?? 0,1) }} m</span>
                        <span class="dt-badge dt-badge-navy">{{ $stok?->stok_rol ?? 0 }} rol</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="pos-right">
        <div class="dt-cart">
            <div class="dt-cart-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cart3 me-2"></i>Keranjang</span>
                <span class="dt-badge dt-badge-gold" id="cartCount">0 item</span>
            </div>
            <div class="dt-cart-body" id="cartBody">
                <div class="dt-cart-empty" id="cartEmpty">
                    <i class="bi bi-cart-x" style="font-size:36px;opacity:.3"></i>
                    <p class="mt-2" style="font-size:13px">Pilih kain untuk ditambahkan</p>
                </div>
                <div id="cartItems"></div>
            </div>
            <div class="dt-cart-footer">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-600" style="font-size:13px">Total Belanja:</span>
                    <span class="dt-cart-total" id="cartTotal">Rp 0</span>
                </div>
                <form id="saleForm" method="POST" action="{{ route('kasir.sales.store') }}" onsubmit="return validateSale()">
                    @csrf
                    <div id="cartInputs"></div>
                    <div class="dt-form-group mb-2">
                        <label class="dt-label" style="font-size:12px">Metode Pembayaran</label>
                        <select name="metode" id="metode" class="dt-select" style="font-size:13px">
                            <option value="tunai">💵 Tunai</option>
                            <option value="transfer">🏦 Transfer</option>
                            <option value="qris">📱 QRIS</option>
                        </select>
                    </div>
                    <div class="dt-form-group mb-2">
                        <label class="dt-label" style="font-size:12px">Jumlah Pembayaran</label>
                        <input type="number" id="jumlahBayar" name="jumlah_bayar" class="dt-input" style="font-size:15px;font-weight:600" placeholder="0" min="0" step="1000" oninput="hitungKembalian()">
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span style="font-size:13px;color:var(--dt-muted)">Kembalian:</span>
                        <span id="kembalian" class="fw-700" style="font-size:16px;color:var(--dt-success)">Rp 0</span>
                    </div>
                    <button type="submit" id="btnBayar" class="dt-btn dt-btn-gold w-100 justify-content-center disabled" style="padding:12px;font-size:15px" disabled>
                        <i class="bi bi-check-circle-fill me-1"></i> Selesaikan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/pos.js') }}"></script>
@endpush
