@extends('layouts.app')
@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori Kain')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Edit Kategori: {{ $category->nama_kategori }}</h1>
        <div class="dt-breadcrumb">Master Data / Kategori / Edit</div>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="dt-card col-md-6">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf
        @method('PUT')
        <div class="dt-form-group">
            <label class="dt-label">Nama Kategori <span class="required">*</span></label>
            <input type="text" name="nama_kategori" class="dt-input @error('nama_kategori') is-invalid @enderror" value="{{ old('nama_kategori', $category->nama_kategori) }}" required>
            @error('nama_kategori') <div class="dt-error-msg">{{ $message }}</div> @enderror
        </div>
        <div class="dt-form-group">
            <label class="dt-label">Deskripsi</label>
            <textarea name="deskripsi" class="dt-textarea">{{ old('deskripsi', $category->deskripsi) }}</textarea>
        </div>
        <div class="mt-4 text-end">
            <button type="submit" class="dt-btn dt-btn-primary"><i class="bi bi-save me-1"></i> Update Kategori</button>
        </div>
    </form>
</div>
@endsection
