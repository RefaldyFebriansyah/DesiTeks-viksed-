@extends('layouts.app')
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori Kain')

@push('styles')
<style>
.preset-chip {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 3px 10px;
    font-size: 11.5px;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
}
.preset-chip:hover {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
}
</style>
@endpush

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title mb-0" style="font-size: 19px;">Tambah Kategori Kain</h1>
        <div class="dt-breadcrumb">Master Data &bull; Kategori Kain &bull; Tambah Baru</div>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-3 g-md-4">
    {{-- Form Tambah Kategori --}}
    <div class="col-lg-7">
        <div class="dt-card">
            <div class="dt-card-header">
                <div>
                    <span class="dt-card-title d-block fw-700">Formulir Kategori Baru</span>
                    <small class="text-muted d-block" style="font-size: 11.5px; margin-top: 2px;">Tambahkan kelompok jenis bahan kain baru ke katalog toko</small>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-2">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-600 text-navy mb-1" style="font-size: 13px;">
                        Nama Kategori <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="nama_kategori" 
                           id="inputNamaKategori"
                           class="dt-input @error('nama_kategori') is-invalid @enderror" 
                           value="{{ old('nama_kategori') }}" 
                           required 
                           placeholder="Misal: Cotton, Linen, Rayon, Sutera">
                    @error('nama_kategori')
                        <div class="dt-error-msg mt-1">{{ $message }}</div>
                    @enderror

                    {{-- Preset Chips --}}
                    <div class="mt-2.5">
                        <span class="text-muted d-block mb-1.5" style="font-size: 11.5px; font-weight: 500;">
                            Pilihan cepat nama kategori:
                        </span>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                            @foreach(['Cotton', 'Linen', 'Rayon', 'Sutera', 'Polyester', 'Denim', 'Wool', 'Jersey', 'Batik', 'Organza'] as $preset)
                                <button type="button" class="preset-chip btn-preset">
                                    + {{ $preset }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-600 text-navy mb-1" style="font-size: 13px;">
                        Deskripsi Kategori
                    </label>
                    <textarea name="deskripsi" 
                              class="dt-textarea" 
                              rows="4" 
                              placeholder="Tuliskan keterangan singkat mengenai kelompok jenis kain ini (opsional)...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="dt-error-msg mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('admin.categories.index') }}" class="dt-btn dt-btn-outline px-3 py-1.5">
                        Batal
                    </a>
                    <button type="submit" class="dt-btn dt-btn-primary px-4 py-1.5">
                        <i class="bi bi-check-circle me-1.5"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Kategori Terdaftar --}}
    <div class="col-lg-5">
        <div class="dt-card">
            <div class="dt-card-header d-flex align-items-center justify-content-between">
                <div>
                    <span class="dt-card-title d-block fw-700">Kategori Terdaftar</span>
                    <small class="text-muted d-block" style="font-size: 11.5px; margin-top: 2px;">Daftar kelompok jenis kain saat ini</small>
                </div>
                <span class="dt-badge dt-badge-navy">{{ $existingCategories->count() }} Kategori</span>
            </div>

            @if($existingCategories->isEmpty())
                <div class="empty-state py-4">
                    <i class="bi bi-inbox fs-3 text-muted"></i>
                    <p class="mt-2 mb-0 text-muted" style="font-size: 12px;">Belum ada kategori terdaftar saat ini</p>
                </div>
            @else
                <div class="mt-2 mb-3">
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <span style="font-size: 12px; font-weight: 600; color: #334155;">Total Produk Kain</span>
                        <span class="fw-700 text-navy" style="font-size: 12.5px;">{{ $totalFabricsCount }} Jenis Kain</span>
                    </div>
                </div>

                <div class="dt-table-wrap" style="max-height: 310px; overflow-y: auto;">
                    <table class="dt-table" style="font-size: 12.5px;">
                        <thead>
                            <tr>
                                <th>Nama Kategori</th>
                                <th class="text-end">Jumlah Kain</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($existingCategories as $cat)
                                <tr>
                                    <td>
                                        <span class="fw-600 text-navy d-block">{{ $cat->nama_kategori }}</span>
                                        @if($cat->deskripsi)
                                            <small class="text-muted d-block text-truncate" style="max-width: 220px; font-size: 11px;">{{ $cat->deskripsi }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end align-middle">
                                        <span class="dt-badge dt-badge-navy" style="font-size: 10.5px;">
                                            {{ $cat->fabrics_count }} kain
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputNama = document.getElementById('inputNamaKategori');
    document.querySelectorAll('.btn-preset').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.innerText.replace(/^\+\s*/, '').trim();
            inputNama.value = text;
            inputNama.focus();
        });
    });
});
</script>
@endpush
@endsection
