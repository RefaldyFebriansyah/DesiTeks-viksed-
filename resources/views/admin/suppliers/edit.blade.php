@extends('layouts.app')
@section('title', 'Edit Supplier')
@section('page-title', 'Edit Data Supplier')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8 col-xl-7">
        <div class="dt-page-header mb-4">
            <div>
                <h1 class="dt-page-title">Edit Supplier: {{ $supplier->nama_supplier }}</h1>
                <div class="dt-breadcrumb">Master Data / Supplier / Edit Data</div>
            </div>
            <a href="{{ route('admin.suppliers.index') }}" class="dt-btn dt-btn-outline">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="dt-card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="p-4 border-bottom bg-light bg-opacity-60">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 44px; height: 44px; flex-shrink: 0;">
                        <i class="bi bi-building fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-navy mb-0" style="font-size: 16px;">{{ $supplier->nama_supplier }}</h5>
                        <p class="text-muted mb-0" style="font-size: 12.5px;">Kode Supplier: <strong class="text-navy">{{ $supplier->kode_supplier }}</strong></p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST" class="p-4 bg-white">
                @csrf
                @method('PUT')

                <div class="dt-form-group mb-3">
                    <label class="dt-label fw-600 mb-1" style="font-size: 12px;"><i class="bi bi-building me-1 text-primary"></i>NAMA SUPPLIER / PABRIK <span class="text-danger">*</span></label>
                    <input type="text" name="nama_supplier" class="form-control @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required style="font-size: 14px;">
                    @error('nama_supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="dt-form-group">
                            <label class="dt-label fw-600 mb-1" style="font-size: 12px;"><i class="bi bi-envelope me-1 text-primary"></i>EMAIL RESMI SUPPLIER</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $supplier->email) }}" placeholder="supplier@pabrik.com" style="font-size: 13.5px;">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dt-form-group">
                            <label class="dt-label fw-600 mb-1" style="font-size: 12px;"><i class="bi bi-geo-alt me-1 text-primary"></i>ASAL KOTA</label>
                            <input type="text" name="asal_kota" class="form-control @error('asal_kota') is-invalid @enderror" value="{{ old('asal_kota', $supplier->asal_kota) }}" placeholder="Contoh: Bandung / Jakarta" style="font-size: 13.5px;">
                            @error('asal_kota') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="dt-form-group mb-3">
                    <label class="dt-label fw-600 mb-1" style="font-size: 12px;"><i class="bi bi-whatsapp me-1 text-success"></i>NO. TELEPON / WHATSAPP</label>
                    <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon', $supplier->no_telepon) }}" placeholder="Contoh: 08123456789" style="font-size: 13.5px;">
                    @error('no_telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="dt-form-group mb-4">
                    <label class="dt-label fw-600 mb-1" style="font-size: 12px;"><i class="bi bi-card-text me-1 text-primary"></i>ALAMAT PABRIK / GUDANG SUPPLIER</label>
                    <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat lengkap pabrik/gudang..." style="font-size: 13.5px;">{{ old('alamat', $supplier->alamat) }}</textarea>
                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.suppliers.index') }}" class="dt-btn dt-btn-outline px-4">
                        Batal
                    </a>
                    <button type="submit" class="dt-btn dt-btn-primary px-4 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
