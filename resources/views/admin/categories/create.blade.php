@extends('layouts.app')
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori Kain')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Tambah Kategori</h1>
        <div class="dt-breadcrumb">Master Data / Kategori / Tambah</div>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="dt-card col-md-6">
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="dt-form-group">
            <label class="dt-label">Nama Kategori <span class="required">*</span></label>
            <input type="text" name="nama_kategori" class="dt-input @error('nama_kategori') is-invalid @enderror" value="{{ old('nama_kategori') }}" required placeholder="Contoh: Cotton">
            @error('nama_kategori') <div class="dt-error-msg">{{ $message }}</div> @enderror
        </div>
        <div class="dt-form-group">
            <label class="dt-label">Deskripsi</label>
            <textarea name="deskripsi" class="dt-textarea" placeholder="Deskripsi singkat kategori kain...">{{ old('deskripsi') }}</textarea>
        </div>
        <div class="mt-4 text-end">
            <button type="submit" class="dt-btn dt-btn-primary"><i class="bi bi-save me-1"></i> Simpan Kategori</button>
        </div>
    </form>
</div>
@endsection
