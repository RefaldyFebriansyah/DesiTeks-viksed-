@extends('layouts.app')
@section('title', 'Tambah Kain')
@section('page-title', 'Tambah Kain Baru')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Tambah Kain</h1>
        <div class="dt-breadcrumb">Master Data / Kain / Tambah</div>
    </div>
    <a href="{{ route('admin.fabrics.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="dt-card">
    <form method="POST" action="{{ route('admin.fabrics.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <div class="dt-form-group">
                    <label class="dt-label">Kode Kain <span class="required">*</span></label>
                    <input type="text" name="kode_kain" class="dt-input @error('kode_kain') is-invalid @enderror" value="{{ old('kode_kain') }}" placeholder="Contoh: CTN-001" required>
                    @error('kode_kain') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="dt-form-group">
                    <label class="dt-label">Nama Kain <span class="required">*</span></label>
                    <input type="text" name="nama_kain" class="dt-input @error('nama_kain') is-invalid @enderror" value="{{ old('nama_kain') }}" placeholder="Contoh: Cotton Combed 30s" required>
                    @error('nama_kain') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="dt-form-group">
                    <label class="dt-label">Kategori Kain <span class="required">*</span></label>
                    <input type="text" name="nama_kategori" list="categoryList" class="dt-input @error('nama_kategori') is-invalid @enderror" value="{{ old('nama_kategori') }}" placeholder="Ketik atau pilih kategori (misal: Cotton, Linen, Rayon)" required autocomplete="off">
                    <datalist id="categoryList">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->nama_kategori }}"></option>
                        @endforeach
                    </datalist>
                    @error('nama_kategori') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="dt-form-group">
                    <label class="dt-label">Jenis Kain</label>
                    <input type="text" name="jenis_kain" class="dt-input" value="{{ old('jenis_kain') }}" placeholder="Contoh: Cotton/Rayon/Linen">
                </div>
            </div>
            <div class="col-md-6">
                <div class="dt-form-group">
                    <label class="dt-label">Warna</label>
                    <input type="text" name="warna" class="dt-input" value="{{ old('warna') }}" placeholder="Contoh: Merah Cabe / Navy">
                </div>
            </div>
            <div class="col-md-6">
                <div class="dt-form-group">
                    <label class="dt-label">Motif</label>
                    <input type="text" name="motif" class="dt-input" value="{{ old('motif') }}" placeholder="Contoh: Polos / Bunga / Salur">
                </div>
            </div>
            <div class="col-md-6">
                <div class="dt-form-group">
                    <label class="dt-label">Harga Per Meter (Rp) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="harga_per_meter" class="dt-input @error('harga_per_meter') is-invalid @enderror" value="{{ old('harga_per_meter', 0) }}" required>
                    @error('harga_per_meter') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="dt-form-group">
                    <label class="dt-label">Harga Per Rol (Rp) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="harga_per_rol" class="dt-input @error('harga_per_rol') is-invalid @enderror" value="{{ old('harga_per_rol', 0) }}" required>
                    @error('harga_per_rol') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="dt-form-group">
                    <label class="dt-label">Stok Minimum Meter</label>
                    <input type="number" name="stok_minimum" class="dt-input" value="{{ old('stok_minimum', 10) }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="dt-form-group">
                    <label class="dt-label">Status <span class="required">*</span></label>
                    <select name="status" class="dt-select" required>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="mt-4 text-end">
            <button type="submit" class="dt-btn dt-btn-primary"><i class="bi bi-save me-1"></i> Simpan Kain</button>
        </div>
    </form>
</div>
@endsection
