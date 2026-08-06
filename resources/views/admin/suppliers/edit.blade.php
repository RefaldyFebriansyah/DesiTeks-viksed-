@extends('layouts.app')
@section('title', 'Edit Supplier')
@section('page-title', 'Edit Data Supplier')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Edit Supplier: {{ $supplier->nama_supplier }}</h1>
        <div class="dt-breadcrumb">Master Data / Supplier / Edit</div>
    </div>
    <a href="{{ route('admin.suppliers.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="dt-card col-md-6">
    <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}">
        @csrf
        @method('PUT')
        <div class="dt-form-group">
            <label class="dt-label">Kode Supplier</label>
            <input type="text" class="dt-input" value="{{ $supplier->kode_supplier }}" disabled readonly>
        </div>
        <div class="dt-form-group">
            <label class="dt-label">Nama Supplier <span class="required">*</span></label>
            <input type="text" name="nama_supplier" class="dt-input @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required>
            @error('nama_supplier') <div class="dt-error-msg">{{ $message }}</div> @enderror
        </div>
        <div class="dt-form-group">
            <label class="dt-label">No Telepon</label>
            <input type="text" name="no_telepon" class="dt-input" value="{{ old('no_telepon', $supplier->no_telepon) }}">
        </div>
        <div class="dt-form-group">
            <label class="dt-label">Alamat</label>
            <textarea name="alamat" class="dt-textarea">{{ old('alamat', $supplier->alamat) }}</textarea>
        </div>
        <div class="mt-4 text-end">
            <button type="submit" class="dt-btn dt-btn-primary"><i class="bi bi-save me-1"></i> Update Supplier</button>
        </div>
    </form>
</div>
@endsection
