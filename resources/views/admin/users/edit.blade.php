@extends('layouts.app')
@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna Sistem')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Edit Pengguna: {{ $user->name }}</h1>
        <div class="dt-breadcrumb">Sistem / Pengguna / Edit</div>
    </div>
    <a href="{{ route('admin.users.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="dt-card col-md-6">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="dt-form-group">
            <label class="dt-label">Nama Lengkap <span class="required">*</span></label>
            <input type="text" name="name" class="dt-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
            @error('name') <div class="dt-error-msg">{{ $message }}</div> @enderror
        </div>
        <div class="dt-form-group">
            <label class="dt-label">Username <span class="required">*</span></label>
            <input type="text" name="username" class="dt-input @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
            @error('username') <div class="dt-error-msg">{{ $message }}</div> @enderror
        </div>
        <div class="dt-form-group">
            <label class="dt-label">Password Baru (Kosongkan jika tidak diubah)</label>
            <input type="password" name="password" class="dt-input @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter">
            @error('password') <div class="dt-error-msg">{{ $message }}</div> @enderror
        </div>
        <div class="dt-form-group">
            <label class="dt-label">Role Hak Akses <span class="required">*</span></label>
            <select name="role" class="dt-select @error('role') is-invalid @enderror" required>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Full Access)</option>
                <option value="gudang" {{ old('role', $user->role) == 'gudang' ? 'selected' : '' }}>Gudang (Stok & Barang Masuk)</option>
                <option value="kasir" {{ old('role', $user->role) == 'kasir' ? 'selected' : '' }}>Kasir (Penjualan)</option>
            </select>
            @error('role') <div class="dt-error-msg">{{ $message }}</div> @enderror
        </div>
        <div class="dt-form-group">
            <label class="dt-label">Status <span class="required">*</span></label>
            <select name="status" class="dt-select" required>
                <option value="aktif" {{ old('status', $user->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ old('status', $user->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        <div class="mt-4 text-end">
            <button type="submit" class="dt-btn dt-btn-primary"><i class="bi bi-save me-1"></i> Update Pengguna</button>
        </div>
    </form>
</div>
@endsection
