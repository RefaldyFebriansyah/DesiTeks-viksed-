@extends('layouts.app')
@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="dt-page-header mb-3">
            <div>
                <h1 class="dt-page-title">Pengaturan Aplikasi & Struk</h1>
                <div class="dt-breadcrumb">Sistem / Pengaturan Toko</div>
            </div>
        </div>
        <div class="dt-card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="p-4 border-bottom bg-light bg-opacity-60">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 42px; height: 42px; flex-shrink: 0;">
                        <i class="bi bi-shop fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-navy mb-0" style="font-size: 15px;">Identitas Toko & Cetakan</h5>
                        <p class="text-muted mb-0" style="font-size: 12px;">Konfigurasi nama toko, nomor telepon (+62), dan footer struk</p>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-white">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    
                    <div class="dt-form-group mb-3">
                        <label class="dt-label fw-600 mb-1.5" for="nama_toko">Nama Toko <span class="text-danger">*</span></label>
                        <input type="text" id="nama_toko" name="nama_toko" value="{{ old('nama_toko', $settings['nama_toko']) }}" class="form-control form-control-lg @error('nama_toko') is-invalid @enderror" required style="font-size: 14px;">
                        @error('nama_toko')
                            <div class="dt-error-msg mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="dt-form-group mb-3">
                        <label class="dt-label fw-600 mb-1.5" for="telepon_toko"><i class="bi bi-whatsapp me-1 text-success"></i>No. Telepon Toko (+62)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold bg-light text-navy border-end-0 px-3">+62</span>
                            <input type="text" id="telepon_toko" name="telepon_toko" value="{{ old('telepon_toko', preg_replace('/^\+?62\s*/', '', $settings['telepon_toko'])) }}" class="form-control border-start-0 @error('telepon_toko') is-invalid @enderror" placeholder="812-3456-7890" style="font-size: 13.5px;">
                        </div>
                        @error('telepon_toko')
                            <div class="dt-error-msg mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="dt-form-group mb-3">
                        <label class="dt-label fw-600 mb-1.5" for="alamat_toko">Alamat Toko</label>
                        <textarea id="alamat_toko" name="alamat_toko" class="form-control @error('alamat_toko') is-invalid @enderror" rows="3" style="font-size: 13.5px;">{{ old('alamat_toko', $settings['alamat_toko']) }}</textarea>
                        @error('alamat_toko')
                            <div class="dt-error-msg mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="dt-form-group mb-4">
                        <label class="dt-label fw-600 mb-1.5" for="catatan_struk">Catatan Struk Belanja</label>
                        <textarea id="catatan_struk" name="catatan_struk" class="form-control @error('catatan_struk') is-invalid @enderror" placeholder="Contoh: Terima kasih atas kunjungan Anda..." rows="3" style="font-size: 13.5px;">{{ old('catatan_struk', $settings['catatan_struk']) }}</textarea>
                        @error('catatan_struk')
                            <div class="dt-error-msg mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pt-3 border-top d-flex align-items-center justify-content-end">
                        <button type="submit" class="dt-btn dt-btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
