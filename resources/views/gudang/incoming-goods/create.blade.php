@extends('layouts.app')
@section('title', 'Tambah Barang Masuk')
@section('page-title', 'Tambah Barang Masuk')

@section('content')
<div class="dt-page-header">
    <div>
        <h1 class="dt-page-title">Tambah Barang Masuk</h1>
        <div class="dt-breadcrumb">Gudang / Barang Masuk / Tambah</div>
    </div>
    <a href="{{ route('gudang.incoming-goods.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="dt-card">
    <form method="POST" action="{{ route('gudang.incoming-goods.store') }}" id="incomingForm" enctype="multipart/form-data">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="dt-form-group">
                    <label class="dt-label">Nomor Faktur / Surat Jalan <span class="required">*</span></label>
                    <input type="text" name="nomor_faktur" class="dt-input @error('nomor_faktur') is-invalid @enderror" value="{{ old('nomor_faktur') }}" required placeholder="Masukkan nomor faktur">
                    @error('nomor_faktur') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="dt-form-group">
                    <label class="dt-label">Nama Supplier / PT Pengirim <span class="required">*</span></label>
                    <input type="text" name="nama_supplier" list="supplierList" class="dt-input @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier') }}" required placeholder="Pilih atau ketik nama supplier" autocomplete="off">
                    <datalist id="supplierList">
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->nama_supplier }}"></option>
                        @endforeach
                    </datalist>
                    @error('nama_supplier') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="dt-form-group">
                    <label class="dt-label">Tanggal Masuk <span class="required">*</span></label>
                    <input type="date" name="tanggal" class="dt-input @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-8">
                <div class="dt-form-group">
                    <label class="dt-label">Catatan / Keterangan</label>
                    <input type="text" name="catatan" class="dt-input" value="{{ old('catatan') }}" placeholder="Catatan tambahan (opsional)">
                </div>
            </div>
            <div class="col-md-4">
                <div class="dt-form-group">
                    <label class="dt-label">Foto Struk / Nota / Surat Jalan</label>
                    <div class="d-flex flex-column gap-2">
                        <input type="file" name="foto_lampiran" id="foto_lampiran_input" class="dt-input @error('foto_lampiran') is-invalid @enderror" accept="image/*" style="padding: 5px 10px;">
                        <button type="button" class="dt-btn dt-btn-gold dt-btn-sm w-100 d-flex align-items-center justify-content-center gap-2" onclick="openCameraModal('foto_lampiran_input')">
                            <i class="bi bi-camera-fill"></i> Ambil Foto via Kamera
                        </button>
                        <div id="foto_preview_container" class="mt-2 d-none position-relative border rounded p-1 bg-white shadow-sm" style="max-width: 150px;">
                            <img id="foto_preview_img" src="" alt="Preview Struk" class="img-fluid rounded" style="max-height: 150px; width: 100%; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" id="btnRemoveFoto" title="Hapus Foto" style="padding: 1px 6px; border-radius: 50%; border: none;">
                                <i class="bi bi-x-lg" style="font-size: 10px; display: block;"></i>
                            </button>
                        </div>
                    </div>
                    @error('foto_lampiran') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <h5 class="fw-700 text-navy mb-3 d-flex justify-content-between align-items-center" style="border-bottom: 1.5px solid var(--dt-border); padding-bottom: 8px;">
            <span>Daftar Item Kain Masuk</span>
            <button type="button" class="dt-btn dt-btn-gold dt-btn-xs" onclick="addItemRow()">
                <i class="bi bi-plus-lg"></i> Tambah Baris
            </button>
        </h5>

        <div class="dt-table-wrap mb-4">
            <table class="dt-table" id="itemTable" style="min-width: 900px;">
                <thead>
                    <tr>
                        <th style="width: 40%">Pilih Kain</th>
                        <th style="width: 15%" class="text-end">Jumlah Rol</th>
                        <th style="width: 15%" class="text-end">Jumlah Meter</th>
                        <th style="width: 20%" class="text-end">Harga Beli/Meter (Rp)</th>
                        <th style="width: 10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="itemContainer">
                    {{-- Default Row 0 --}}
                    <tr class="item-row" id="row_0">
                        <td>
                            <select name="items[0][fabric_id]" class="dt-select fabric-select" onchange="toggleNewFabricRow(0, this.value)" required style="font-size:13px; padding:6px 10px;">
                                <option value="">-- Pilih Kain --</option>
                                <option value="new" style="font-weight:600;color:var(--dt-navy)">+ Tambah Kain Baru</option>
                                @foreach($fabrics as $f)
                                    <option value="{{ $f->id }}">{{ $f->kode_kain }} - {{ $f->nama_kain }} ({{ $f->category->nama_kategori ?? '-' }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[0][jumlah_rol]" class="dt-input text-end" value="0" min="0" required style="font-size:13px; padding:6px 10px;">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="items[0][jumlah_meter]" class="dt-input text-end" value="0" min="0.01" required style="font-size:13px; padding:6px 10px;">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="items[0][harga_beli]" class="dt-input text-end" value="0" min="0" required style="font-size:13px; padding:6px 10px;">
                        </td>
                        <td class="text-center">
                            <button type="button" class="dt-btn dt-btn-danger dt-btn-xs" onclick="removeItemRow(0)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    {{-- Sub-row untuk form Kain Baru (hidden default) --}}
                    <tr id="new_fabric_row_0" style="display:none; background-color:#f8fafc; border-top:none;">
                        <td colspan="5" style="padding: 10px 16px;">
                            <div style="border-left: 3px solid var(--dt-gold); padding-left: 14px;">
                                <div style="font-size:11px; font-weight:700; color:var(--dt-navy); text-transform:uppercase; margin-bottom:8px;">Detail Spesifikasi Kain Baru</div>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="text" name="items[0][nama_kain]" class="dt-input" placeholder="Nama Kain" style="font-size:12.5px; padding:5px 8px;">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="items[0][nama_kategori]" list="categoryList" class="dt-input" placeholder="Kategori" style="font-size:12.5px; padding:5px 8px;">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="items[0][warna]" class="dt-input" placeholder="Warna" style="font-size:12.5px; padding:5px 8px;">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" step="0.01" name="items[0][harga_per_meter]" class="dt-input" placeholder="Harga Jual per Meter (Rp)" style="font-size:12.5px; padding:5px 8px;">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" step="0.01" name="items[0][harga_per_rol]" class="dt-input" placeholder="Harga Jual per Rol (Rp)" style="font-size:12.5px; padding:5px 8px;">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <datalist id="categoryList">
            @foreach($categories as $cat)
                <option value="{{ $cat->nama_kategori }}"></option>
            @endforeach
        </datalist>

        <div class="text-end">
            <button type="submit" class="dt-btn dt-btn-primary" style="padding:10px 24px;">
                <i class="bi bi-check-circle me-1"></i> Simpan Barang Masuk
            </button>
        </div>
    </form>
</div>

<script>
let itemIndex = 1;

function addItemRow() {
    const container = document.getElementById('itemContainer');
    const idx = itemIndex;

    // Row Utama
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.id = `row_${idx}`;
    tr.innerHTML = `
        <td>
            <select name="items[${idx}][fabric_id]" class="dt-select fabric-select" onchange="toggleNewFabricRow(${idx}, this.value)" required style="font-size:13px; padding:6px 10px;">
                <option value="">-- Pilih Kain --</option>
                <option value="new" style="font-weight:600;color:var(--dt-navy)">+ Tambah Kain Baru</option>
                @foreach($fabrics as $f)
                    <option value="{{ $f->id }}">{{ $f->kode_kain }} - {{ $f->nama_kain }} ({{ $f->category->nama_kategori ?? '-' }})</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[${idx}][jumlah_rol]" class="dt-input text-end" value="0" min="0" required style="font-size:13px; padding:6px 10px;">
        </td>
        <td>
            <input type="number" step="0.01" name="items[${idx}][jumlah_meter]" class="dt-input text-end" value="0" min="0.01" required style="font-size:13px; padding:6px 10px;">
        </td>
        <td>
            <input type="number" step="0.01" name="items[${idx}][harga_beli]" class="dt-input text-end" value="0" min="0" required style="font-size:13px; padding:6px 10px;">
        </td>
        <td class="text-center">
            <button type="button" class="dt-btn dt-btn-danger dt-btn-xs" onclick="removeItemRow(${idx})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    container.appendChild(tr);

    // Row Spesifikasi (Hidden)
    const subTr = document.createElement('tr');
    subTr.id = `new_fabric_row_${idx}`;
    subTr.style.display = 'none';
    subTr.style.backgroundColor = '#f8fafc';
    subTr.style.borderTop = 'none';
    subTr.innerHTML = `
        <td colspan="5" style="padding: 10px 16px;">
            <div style="border-left: 3px solid var(--dt-gold); padding-left: 14px;">
                <div style="font-size:11px; font-weight:700; color:var(--dt-navy); text-transform:uppercase; margin-bottom:8px;">Detail Spesifikasi Kain Baru</div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="items[${idx}][nama_kain]" class="dt-input" placeholder="Nama Kain" style="font-size:12.5px; padding:5px 8px;">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="items[${idx}][nama_kategori]" list="categoryList" class="dt-input" placeholder="Kategori" style="font-size:12.5px; padding:5px 8px;">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="items[${idx}][warna]" class="dt-input" placeholder="Warna" style="font-size:12.5px; padding:5px 8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="number" step="0.01" name="items[${idx}][harga_per_meter]" class="dt-input" placeholder="Harga Jual per Meter (Rp)" style="font-size:12.5px; padding:5px 8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="number" step="0.01" name="items[${idx}][harga_per_rol]" class="dt-input" placeholder="Harga Jual per Rol (Rp)" style="font-size:12.5px; padding:5px 8px;">
                    </div>
                </div>
            </div>
        </td>
    `;
    container.appendChild(subTr);

    itemIndex++;
}

function toggleNewFabricRow(idx, val) {
    const subRow = document.getElementById(`new_fabric_row_${idx}`);
    if (val === 'new') {
        subRow.style.display = '';
        subRow.querySelectorAll('input').forEach(i => i.required = true);
    } else {
        subRow.style.display = 'none';
        subRow.querySelectorAll('input').forEach(i => i.required = false);
    }
}

function removeItemRow(idx) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        const row = document.getElementById(`row_${idx}`);
        const subRow = document.getElementById(`new_fabric_row_${idx}`);
        if (row) row.remove();
        if (subRow) subRow.remove();
    } else {
        alert('Minimal harus ada 1 item kain masuk.');
    }
}

// Preview handler for receipt image
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('foto_lampiran_input');
    const previewContainer = document.getElementById('foto_preview_container');
    const previewImg = document.getElementById('foto_preview_img');
    const removeBtn = document.getElementById('btnRemoveFoto');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                previewImg.src = '';
                previewContainer.classList.add('d-none');
            }
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            if (fileInput) {
                fileInput.value = ''; // clear input
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }
});
</script>

@include('partials.camera-modal')
@endsection
