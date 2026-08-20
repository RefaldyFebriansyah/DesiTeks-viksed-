@extends('layouts.app')
@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Pengaturan Aplikasi & Struk</h1>
        <div class="dt-breadcrumb">Sistem / Pengaturan Toko</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="dt-card">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-sliders me-2 text-gold"></i>Identitas Toko & Cetakan</span>
            </div>
            
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                
                <div class="dt-form-group">
                    <label class="dt-label" for="nama_toko">Nama Toko <span class="required">*</span></label>
                    <input type="text" id="nama_toko" name="nama_toko" value="{{ old('nama_toko', $settings['nama_toko']) }}" class="dt-input @error('nama_toko') is-invalid @enderror" required>
                    @error('nama_toko')
                        <div class="dt-error-msg">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="dt-form-group">
                    <label class="dt-label" for="telepon_toko">No. Telepon Toko</label>
                    <input type="text" id="telepon_toko" name="telepon_toko" value="{{ old('telepon_toko', $settings['telepon_toko']) }}" class="dt-input @error('telepon_toko') is-invalid @enderror">
                    @error('telepon_toko')
                        <div class="dt-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="dt-form-group">
                    <label class="dt-label" for="alamat_toko">Alamat Toko</label>
                    <textarea id="alamat_toko" name="alamat_toko" class="dt-textarea @error('alamat_toko') is-invalid @enderror">{{ old('alamat_toko', $settings['alamat_toko']) }}</textarea>
                    @error('alamat_toko')
                        <div class="dt-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="dt-form-group">
                    <label class="dt-label" for="catatan_struk">Catatan Kaki Struk Belanja</label>
                    <textarea id="catatan_struk" name="catatan_struk" class="dt-textarea @error('catatan_struk') is-invalid @enderror" placeholder="Contoh: Barang yang sudah dibeli tidak dapat ditukar/dikembalikan..." style="min-height: 80px;">{{ old('catatan_struk', $settings['catatan_struk']) }}</textarea>
                    @error('catatan_struk')
                        <div class="dt-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 pt-3 border-top border-light">
                    <button type="submit" class="dt-btn dt-btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="dt-card">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-info-circle me-2 text-navy"></i>Panduan Konfigurasi</span>
            </div>
            <div class="p-2">
                <p>Pengaturan ini bersifat global dan digunakan di beberapa tempat di aplikasi:</p>
                <ul class="ps-3" style="font-size:13.5px; line-height:1.8;">
                    <li><strong>Nama Toko:</strong> Ditampilkan di pojok kiri atas (sidebar brand), tab browser, dan paling atas pada cetak struk belanja POS.</li>
                    <li><strong>No. Telepon & Alamat Toko:</strong> Ditampilkan pada header struk belanja fisik saat transaksi diselesaikan.</li>
                    <li><strong>Catatan Kaki Struk:</strong> Ditampilkan di bagian terbawah struk belanja (keterangan retur, ucapan terima kasih, dll).</li>
                </ul>
                <div class="alert alert-info py-3 px-3 mt-4 border-0 d-flex gap-2 text-navy" style="background: rgba(15,39,68,0.06); border-radius: 8px; font-size:13px;">
                    <i class="bi bi-shield-fill-check fs-5 text-gold"></i>
                    <div>
                        <strong>Keamanan Data:</strong> Setiap perubahan pengaturan toko akan dicatat secara otomatis dalam log aktivitas sistem untuk keperluan audit berkala.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
