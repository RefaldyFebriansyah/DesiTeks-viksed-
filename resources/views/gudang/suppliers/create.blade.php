@extends('layouts.app')
@section('title', 'Tambah Supplier')
@section('page-title', 'Tambah Supplier Baru (Gudang)')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Tambah Supplier / PT</h1>
        <div class="dt-breadcrumb">Gudang / Supplier / Tambah</div>
    </div>
    <a href="{{ route('gudang.suppliers.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="dt-card col-md-6">
    <form method="POST" action="{{ route('gudang.suppliers.store') }}">
        @csrf
        <div class="dt-form-group">
            <label class="dt-label">Nama Supplier / PT / Pabrik <span class="required">*</span></label>
            <input type="text" name="nama_supplier" class="dt-input @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier') }}" required placeholder="Contoh: PT. Tekstil Nusantara">
            @error('nama_supplier') <div class="dt-error-msg">{{ $message }}</div> @enderror
        </div>
        <div class="dt-form-group">
            <label class="dt-label">No Telepon / Kontak</label>
            <input type="text" name="no_telepon" class="dt-input" value="{{ old('no_telepon') }}" placeholder="Contoh: 08123456789 / 022-7654321">
        </div>
        <div class="dt-form-group">
            <label class="dt-label">Alamat Lengkap</label>
            <textarea name="alamat" class="dt-textarea" placeholder="Alamat pabrik / supplier...">{{ old('alamat') }}</textarea>
        </div>
        <div class="mt-4 text-end">
            <button type="submit" class="dt-btn dt-btn-primary"><i class="bi bi-save me-1"></i> Simpan Supplier</button>
        </div>
    </form>
</div>
@endsection
