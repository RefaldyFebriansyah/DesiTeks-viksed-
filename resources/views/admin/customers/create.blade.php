@extends('layouts.app')
@section('title', 'Tambah Pelanggan')
@section('page-title', 'Tambah Pelanggan')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Tambah Pelanggan Baru</h1>
        <div class="dt-breadcrumb">Pelanggan / Baru</div>
    </div>
    <a href="{{ route('admin.customers.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="dt-card">
            <div class="dt-card-header">
                <span class="dt-card-title"><i class="bi bi-person-plus me-2 text-gold"></i>Formulir Data Pelanggan</span>
            </div>

            <form method="POST" action="{{ route('admin.customers.store') }}">
                @csrf

                <div class="dt-form-group">
                    <label class="dt-label" for="kode_pelanggan">Kode Pelanggan <span class="text-muted" style="font-size:11px">(Opsional)</span></label>
                    <input type="text" id="kode_pelanggan" name="kode_pelanggan" value="{{ old('kode_pelanggan') }}" class="dt-input @error('kode_pelanggan') is-invalid @enderror" placeholder="Contoh: CUST-0001 (Otomatis jika kosong)">
                    @error('kode_pelanggan')
                        <div class="dt-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="dt-form-group">
                    <label class="dt-label" for="nama">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" class="dt-input @error('nama') is-invalid @enderror" required placeholder="Masukkan nama pelanggan...">
                    @error('nama')
                        <div class="dt-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="dt-form-group">
                    <label class="dt-label" for="telepon">Nomor Telepon / WhatsApp</label>
                    <input type="text" id="telepon" name="telepon" value="{{ old('telepon') }}" class="dt-input @error('telepon') is-invalid @enderror" placeholder="Contoh: 0812xxxxxxxx">
                    @error('telepon')
                        <div class="dt-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="dt-form-group">
                    <label class="dt-label" for="alamat">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat" class="dt-textarea @error('alamat') is-invalid @enderror" placeholder="Masukkan alamat lengkap pelanggan...">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="dt-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label class="dt-label" for="tipe">Tipe Pelanggan <span class="required">*</span></label>
                            <select id="tipe" name="tipe" class="dt-select @error('tipe') is-invalid @enderror" required onchange="handleTipeChange()">
                                <option value="eceran" {{ old('tipe') == 'eceran' ? 'selected' : '' }}>Eceran (Umum)</option>
                                <option value="grosir" {{ old('tipe') == 'grosir' ? 'selected' : '' }}>Grosir (Toko)</option>
                                <option value="member" {{ old('tipe') == 'member' ? 'selected' : '' }}>Member (Loyalitas)</option>
                            </select>
                            @error('tipe')
                                <div class="dt-error-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-sm-6" id="memberDiscountWrapper" style="display: none;">
                        <div class="dt-form-group">
                            <label class="dt-label" for="diskon_member">Diskon Member (%) <span class="required">*</span></label>
                            <input type="number" id="diskon_member" name="diskon_member" value="{{ old('diskon_member', 0) }}" class="dt-input @error('diskon_member') is-invalid @enderror" min="0" max="100" step="0.1">
                            @error('diskon_member')
                                <div class="dt-error-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top border-light">
                    <button type="submit" class="dt-btn dt-btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Pelanggan</button>
                    <a href="{{ route('admin.customers.index') }}" class="dt-btn dt-btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleTipeChange() {
    const tipe = document.getElementById('tipe').value;
    const wrapper = document.getElementById('memberDiscountWrapper');
    const input = document.getElementById('diskon_member');
    
    if (tipe === 'member') {
        wrapper.style.display = 'block';
        if (parseFloat(input.value) === 0) {
            input.value = 5.0; // Default diskon 5% untuk member baru
        }
    } else {
        wrapper.style.display = 'none';
    }
}

// Trigger saat load pertama kali untuk mengantisipasi nilai 'old'
document.addEventListener('DOMContentLoaded', handleTipeChange);
</script>
@endsection
