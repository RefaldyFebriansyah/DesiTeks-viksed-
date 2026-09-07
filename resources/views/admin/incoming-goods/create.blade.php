@extends('layouts.app')
@section('title', 'Tambah Barang Masuk')
@section('page-title', 'Catat Barang Masuk')

@section('content')
<div class="dt-page-header mb-4">
    <div>
        <h1 class="dt-page-title">Tambah Barang Masuk</h1>
        <div class="dt-breadcrumb">Gudang / Barang Masuk / Tambah</div>
    </div>
    <a href="{{ route('admin.incoming-goods.index') }}" class="dt-btn dt-btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="dt-card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
    <form method="POST" action="{{ route('admin.incoming-goods.store') }}" id="incomingForm" enctype="multipart/form-data">
        @csrf
        
        <div class="p-4">
            <!-- Section 1: Informasi Transaksi & Supplier -->
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-bold" style="width: 26px; height: 26px; font-size: 12px;">1</span>
                    <h6 class="fw-bold text-navy mb-0" style="font-size: 14px; letter-spacing: -0.1px;">Informasi Faktur & Supplier</h6>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="dt-form-group">
                            <label class="form-label text-secondary fw-600 mb-1" style="font-size: 12.5px;">Nama Supplier / PT Pengirim <span class="text-danger">*</span></label>
                            <input type="text" name="nama_supplier" id="namaSupplierInput" list="supplierList" class="form-control @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier') }}" required placeholder="Pilih atau ketik nama PT / Supplier" autocomplete="off" style="font-size: 13.5px; border-radius: 8px;" oninput="onSupplierChange()" onchange="onSupplierChange()">
                            <datalist id="supplierList">
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->nama_supplier }}">{{ $sup->incoming_goods_count }}x Transaksi sebelumnya</option>
                                @endforeach
                            </datalist>
                            @error('nama_supplier') <div class="dt-error-msg mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dt-form-group">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label text-secondary fw-600 mb-0" style="font-size: 12.5px;">Nomor Faktur / Surat Jalan <span class="text-danger">*</span></label>
                                <span class="badge bg-light text-muted border py-0.5 px-1.5" id="fakturBadge" style="font-size: 10px; font-weight: 500;">Otomatis per Supplier</span>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0" style="font-size: 13px; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                    <i class="bi bi-file-earmark-text text-primary"></i>
                                </span>
                                <input type="text" name="nomor_faktur" id="nomorFakturInput" class="form-control border-start-0 @error('nomor_faktur') is-invalid @enderror" value="{{ old('nomor_faktur') }}" required placeholder="Otomatis sesuai PT" style="font-size: 13px; font-weight: 500; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                            </div>
                            <div class="mt-1" id="fakturHelperContainer">
                                <small class="text-muted" id="fakturHelperText" style="font-size: 11px;">Otomatis mengikuti riwayat transaksi supplier ke Desiteks</small>
                            </div>
                            @error('nomor_faktur') <div class="dt-error-msg mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dt-form-group">
                            <label class="form-label text-secondary fw-600 mb-1" style="font-size: 12.5px;">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required style="font-size: 13.5px; border-radius: 8px;">
                            @error('tanggal') <div class="dt-error-msg mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="dt-form-group">
                            <label class="form-label text-secondary fw-600 mb-1" style="font-size: 12.5px;">Catatan / Keterangan</label>
                            <input type="text" name="catatan" class="form-control" value="{{ old('catatan') }}" placeholder="Catatan tambahan penerimaan barang (opsional)" style="font-size: 13.5px; border-radius: 8px;">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="dt-form-group">
                            <label class="form-label text-secondary fw-600 mb-1" style="font-size: 12.5px;">Lampiran Struk / Nota / Surat Jalan</label>
                            <div class="d-flex align-items-center gap-2">
                                <label class="btn btn-sm btn-light border flex-grow-1 text-start text-truncate text-secondary mb-0 py-1.5 px-3" style="font-size: 12.5px; background: #f8fafc; border-color: #e2e8f0; border-radius: 8px; cursor: pointer;">
                                    <i class="bi bi-file-earmark-arrow-up me-1.5 text-primary"></i>
                                    <span id="foto_filename_text">Pilih File Nota / Struk</span>
                                    <input type="file" name="foto_lampiran" id="foto_lampiran_input" accept="image/*" class="d-none">
                                </label>
                                <span class="text-muted" style="font-size: 11.5px;">atau</span>
                                <button type="button" class="btn btn-sm btn-light border text-navy flex-shrink-0 py-1.5 px-3" onclick="openCameraModal('foto_lampiran_input')" style="font-size: 12.5px; background: #f8fafc; border-color: #e2e8f0; border-radius: 8px;">
                                    <i class="bi bi-camera me-1"></i> Kamera
                                </button>
                            </div>
                            <div id="foto_preview_container" class="mt-2 d-none position-relative border rounded p-1 bg-white shadow-xs" style="max-width: 130px;">
                                <img id="foto_preview_img" src="" alt="Preview Struk" class="img-fluid rounded" style="max-height: 120px; width: 100%; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" id="btnRemoveFoto" title="Hapus Foto" style="padding: 1px 5px; border-radius: 50%; border: none;">
                                    <i class="bi bi-x-lg" style="font-size: 10px; display: block;"></i>
                                </button>
                            </div>
                            @error('foto_lampiran') <div class="dt-error-msg mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4" style="border-color: #f1f5f9;">

            <!-- Section 2: Rincian Item Kain Masuk -->
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-bold" style="width: 26px; height: 26px; font-size: 12px;">2</span>
                        <h6 class="fw-bold text-navy mb-0" style="font-size: 14px; letter-spacing: -0.1px;">Rincian Item Kain Masuk</h6>
                    </div>
                    <span class="badge bg-light text-muted border py-1 px-2" id="itemCountBadge" style="font-size: 11px;">1 Item Kain</span>
                </div>

                <div class="table-responsive border rounded-3 bg-white mb-2" style="border-color: #e2e8f0 !important;">
                    <table class="table align-middle mb-0" id="itemsTable" style="border-color: #f1f5f9;">
                        <thead style="background: #f8fafc; font-size: 11.5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-3 py-2.5" style="width: 42%;">Item Kain</th>
                                <th class="text-center py-2.5" style="width: 15%;">Jumlah Rol</th>
                                <th class="text-center py-2.5" style="width: 17%;">Jumlah Meter</th>
                                <th class="text-center py-2.5" style="width: 20%;">Harga Beli / Meter</th>
                                <th class="text-center py-2.5" style="width: 6%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemContainer">
                            <tr class="item-row" id="row_0">
                                <td class="ps-3 py-2">
                                    <select name="items[0][fabric_id]" class="form-select form-select-sm fabric-select" onchange="toggleNewFabricRow(0, this.value)" required style="font-size: 13px; border-radius: 6px;">
                                        <option value="">-- Pilih Kain --</option>
                                        <option value="new" class="fw-bold text-primary">+ Buat Kain Baru di Sistem</option>
                                        @foreach($fabrics as $f)
                                            <option value="{{ $f->id }}">{{ $f->kode_kain }} - {{ $f->nama_kain }} ({{ $f->category->nama_kategori ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2">
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="items[0][jumlah_rol]" class="form-control text-end item-rol" value="0" min="0" required oninput="calculateTotals()" style="font-size: 13px;">
                                        <span class="input-group-text bg-light text-muted px-2" style="font-size: 11.5px;">rol</span>
                                    </div>
                                </td>
                                <td class="py-2">
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" name="items[0][jumlah_meter]" class="form-control text-end item-meter" value="0" min="0.01" required oninput="calculateTotals()" style="font-size: 13px;">
                                        <span class="input-group-text bg-light text-muted px-2" style="font-size: 11.5px;">m</span>
                                    </div>
                                </td>
                                <td class="py-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted px-2" style="font-size: 11.5px;">Rp</span>
                                        <input type="number" step="0.01" name="items[0][harga_beli]" class="form-control text-end item-harga" value="0" min="0" required oninput="calculateTotals()" style="font-size: 13px;">
                                    </div>
                                </td>
                                <td class="text-center py-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger p-1 border-0" onclick="removeItemRow(0)" title="Hapus Item" style="line-height: 1;">
                                        <i class="bi bi-trash3 fs-6"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr id="new_fabric_row_0" style="display:none; background-color: #f8fafc;">
                                <td colspan="5" class="p-3">
                                    <div class="p-3 bg-white border border-primary-subtle rounded-3 shadow-xs">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-stars text-primary"></i>
                                            <span class="fw-bold text-navy" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Detail Spesifikasi Kain Baru</span>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <input type="text" name="items[0][nama_kain]" class="form-control form-control-sm" placeholder="Nama Kain" style="font-size: 12.5px;">
                                            </div>
                                            <div class="col-md-4">
                                                <select name="items[0][nama_kategori]" class="form-select form-select-sm" style="font-size: 12.5px;">
                                                    <option value="">-- Pilih Kategori Kain --</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->nama_kategori }}">{{ $cat->nama_kategori }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="items[0][warna]" class="form-control form-control-sm" placeholder="Warna" style="font-size: 12.5px;">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" step="0.01" name="items[0][harga_per_meter]" class="form-control form-control-sm" placeholder="Harga Jual per Meter (Rp)" style="font-size: 12.5px;">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" step="0.01" name="items[0][harga_per_rol]" class="form-control form-control-sm" placeholder="Harga Jual per Rol (Rp)" style="font-size: 12.5px;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
                            <tr>
                                <td class="ps-3 py-2.5 fw-bold text-muted" style="font-size: 12px;">RINGKASAN TOTAL</td>
                                <td class="text-end py-2.5 fw-bold text-navy" style="font-size: 13px;">
                                    <span id="totalRolDisplay">0</span> <span class="text-muted fw-normal" style="font-size: 11px;">rol</span>
                                </td>
                                <td class="text-end py-2.5 fw-bold text-navy" style="font-size: 13px;">
                                    <span id="totalMeterDisplay">0</span> <span class="text-muted fw-normal" style="font-size: 11px;">m</span>
                                </td>
                                <td class="text-end py-2.5 fw-bold text-success" style="font-size: 13.5px;">
                                    Rp <span id="totalBeliDisplay">0</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Hidden inputs for calculated totals -->
                <input type="hidden" name="total_rol" id="hiddenTotalRol" value="0">
                <input type="hidden" name="total_meter" id="hiddenTotalMeter" value="0">
                <input type="hidden" name="total_pembelian" id="hiddenTotalBeli" value="0">

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-3 py-1.5 fw-medium" onclick="addItemRow()" style="font-size: 12.5px;">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Item Kain Lain
                    </button>
                    <div class="text-muted" style="font-size: 11.5px;">
                        <i class="bi bi-info-circle me-1"></i>Stok otomatis bertambah sesuai kain yang dipilih
                    </div>
                </div>

                <datalist id="categoryList">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->nama_kategori }}"></option>
                    @endforeach
                </datalist>
            </div>

            <!-- Bottom Actions -->
            <div class="pt-4 mt-4 border-top d-flex align-items-center justify-content-between">
                <a href="{{ route('admin.incoming-goods.index') }}" class="btn btn-sm btn-light border px-4 py-2 rounded-3 text-secondary fw-medium" style="font-size: 13px;">Batal</a>
                <button type="submit" class="dt-btn dt-btn-primary px-4 py-2.5 rounded-3 fw-semibold">
                    <i class="bi bi-check2-circle me-1.5"></i> Simpan Barang Masuk
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let itemIndex = 1;
const categoryOptionsHtml = `@foreach($categories as $cat)<option value="{{ $cat->nama_kategori }}">{{ $cat->nama_kategori }}</option>@endforeach`;

function calculateTotals() {
    let totalRol = 0;
    let totalMeter = 0;
    let totalBeli = 0;

    const rows = document.querySelectorAll('.item-row');
    rows.forEach(row => {
        const rolInput = row.querySelector('.item-rol');
        const meterInput = row.querySelector('.item-meter');
        const hargaInput = row.querySelector('.item-harga');

        const rol = parseInt(rolInput ? rolInput.value : 0) || 0;
        const meter = parseFloat(meterInput ? meterInput.value : 0) || 0;
        const harga = parseFloat(hargaInput ? hargaInput.value : 0) || 0;

        totalRol += rol;
        totalMeter += meter;
        totalBeli += (meter * harga);
    });

    const rolDisp = document.getElementById('totalRolDisplay');
    const meterDisp = document.getElementById('totalMeterDisplay');
    const beliDisp = document.getElementById('totalBeliDisplay');

    if (rolDisp) rolDisp.textContent = totalRol;
    if (meterDisp) meterDisp.textContent = totalMeter.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 });
    if (beliDisp) beliDisp.textContent = Math.round(totalBeli).toLocaleString('id-ID');

    const hRol = document.getElementById('hiddenTotalRol');
    const hMeter = document.getElementById('hiddenTotalMeter');
    const hBeli = document.getElementById('hiddenTotalBeli');

    if (hRol) hRol.value = totalRol;
    if (hMeter) hMeter.value = totalMeter;
    if (hBeli) hBeli.value = Math.round(totalBeli);

    const badge = document.getElementById('itemCountBadge');
    if (badge) badge.textContent = rows.length + ' Item Kain';
}

function addItemRow() {
    const container = document.getElementById('itemContainer');
    const idx = itemIndex;

    // Row Utama
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.id = `row_${idx}`;
    tr.innerHTML = `
        <td class="ps-3 py-2">
            <select name="items[${idx}][fabric_id]" class="form-select form-select-sm fabric-select" onchange="toggleNewFabricRow(${idx}, this.value)" required style="font-size: 13px; border-radius: 6px;">
                <option value="">-- Pilih Kain --</option>
                <option value="new" class="fw-bold text-primary">+ Buat Kain Baru di Sistem</option>
                @foreach($fabrics as $f)
                    <option value="{{ $f->id }}">{{ $f->kode_kain }} - {{ $f->nama_kain }} ({{ $f->category->nama_kategori ?? '-' }})</option>
                @endforeach
            </select>
        </td>
        <td class="py-2">
            <div class="input-group input-group-sm">
                <input type="number" name="items[${idx}][jumlah_rol]" class="form-control text-end item-rol" value="0" min="0" required oninput="calculateTotals()" style="font-size: 13px;">
                <span class="input-group-text bg-light text-muted px-2" style="font-size: 11.5px;">rol</span>
            </div>
        </td>
        <td class="py-2">
            <div class="input-group input-group-sm">
                <input type="number" step="0.01" name="items[${idx}][jumlah_meter]" class="form-control text-end item-meter" value="0" min="0.01" required oninput="calculateTotals()" style="font-size: 13px;">
                <span class="input-group-text bg-light text-muted px-2" style="font-size: 11.5px;">m</span>
            </div>
        </td>
        <td class="py-2">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted px-2" style="font-size: 11.5px;">Rp</span>
                <input type="number" step="0.01" name="items[${idx}][harga_beli]" class="form-control text-end item-harga" value="0" min="0" required oninput="calculateTotals()" style="font-size: 13px;">
            </div>
        </td>
        <td class="text-center py-2">
            <button type="button" class="btn btn-sm btn-outline-danger p-1 border-0" onclick="removeItemRow(${idx})" title="Hapus Item" style="line-height: 1;">
                <i class="bi bi-trash3 fs-6"></i>
            </button>
        </td>
    `;
    container.appendChild(tr);

    // Row Spesifikasi (Hidden)
    const subTr = document.createElement('tr');
    subTr.id = `new_fabric_row_${idx}`;
    subTr.style.display = 'none';
    subTr.style.backgroundColor = '#f8fafc';
    subTr.innerHTML = `
        <td colspan="5" class="p-3">
            <div class="p-3 bg-white border border-primary-subtle rounded-3 shadow-xs">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-stars text-primary"></i>
                    <span class="fw-bold text-navy" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Detail Spesifikasi Kain Baru</span>
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="items[${idx}][nama_kain]" class="form-control form-control-sm" placeholder="Nama Kain" style="font-size: 12.5px;">
                    </div>
                    <div class="col-md-4">
                        <select name="items[${idx}][nama_kategori]" class="form-select form-select-sm" style="font-size: 12.5px;">
                            <option value="">-- Pilih Kategori Kain --</option>
                            ${categoryOptionsHtml}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="items[${idx}][warna]" class="form-control form-control-sm" placeholder="Warna" style="font-size: 12.5px;">
                    </div>
                    <div class="col-md-6">
                        <input type="number" step="0.01" name="items[${idx}][harga_per_meter]" class="form-control form-control-sm" placeholder="Harga Jual per Meter (Rp)" style="font-size: 12.5px;">
                    </div>
                    <div class="col-md-6">
                        <input type="number" step="0.01" name="items[${idx}][harga_per_rol]" class="form-control form-control-sm" placeholder="Harga Jual per Rol (Rp)" style="font-size: 12.5px;">
                    </div>
                </div>
            </div>
        </td>
    `;
    container.appendChild(subTr);

    itemIndex++;
    calculateTotals();
}

function toggleNewFabricRow(idx, val) {
    const subRow = document.getElementById(`new_fabric_row_${idx}`);
    if (!subRow) return;
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
        calculateTotals();
    } else {
        alert('Minimal harus ada 1 item kain masuk.');
    }
}

// Preview handler for receipt image
document.addEventListener('DOMContentLoaded', () => {
    calculateTotals();

    const fileInput = document.getElementById('foto_lampiran_input');
    const previewContainer = document.getElementById('foto_preview_container');
    const previewImg = document.getElementById('foto_preview_img');
    const removeBtn = document.getElementById('btnRemoveFoto');
    const filenameText = document.getElementById('foto_filename_text');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                if (filenameText) filenameText.textContent = this.files[0].name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                if (filenameText) filenameText.textContent = 'Pilih File Nota / Struk';
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

    // Inisialisasi No Faktur otomatis jika supplier sudah terpilih / reload
    const supplierInput = document.getElementById('namaSupplierInput');
    const fakturInput = document.getElementById('nomorFakturInput');
    if (supplierInput && supplierInput.value.trim() && fakturInput && !fakturInput.value.trim()) {
        onSupplierChange();
    }
    if (fakturInput) {
        fakturInput.addEventListener('input', function() {
            this.dataset.autoGenerated = 'false';
        });
    }
});

// Mapping riwayat jumlah transaksi barang masuk dari tiap supplier ke Desiteks
const supplierCounts = {
    @foreach($suppliers as $sup)
        @json(trim($sup->nama_supplier)): {{ (int) $sup->incoming_goods_count }},
    @endforeach
};

function extractCompanyPrefix(companyName) {
    if (!companyName || !companyName.trim()) return 'SUP';
    let cleaned = companyName.trim().replace(/^(pt\.?|cv\.?|ud\.?|fa\.?|toko|tb\.?)\s+/i, '');
    let words = cleaned.split(/[\s\-_.]+/).filter(Boolean);
    let letters = '';
    for (let w of words) {
        if (w.length > 0) letters += w[0].toUpperCase();
    }
    if (letters.length >= 2) return letters.substring(0, 4);
    if (cleaned.length >= 3) return cleaned.substring(0, 3).toUpperCase();
    return 'SUP';
}

function onSupplierChange() {
    const supplierInput = document.getElementById('namaSupplierInput');
    const fakturInput = document.getElementById('nomorFakturInput');
    const badge = document.getElementById('fakturBadge');
    const helper = document.getElementById('fakturHelperText');
    if (!supplierInput || !fakturInput) return;

    const companyName = supplierInput.value.trim();
    if (!companyName) {
        fakturInput.value = '';
        if (badge) {
            badge.textContent = 'Otomatis per Supplier';
            badge.className = 'badge bg-light text-muted border py-0.5 px-1.5';
        }
        if (helper) {
            helper.textContent = 'Otomatis mengikuti riwayat transaksi supplier ke Desiteks';
        }
        return;
    }

    // Hitung jumlah transaksi sebelumnya dari PT ini ke Desiteks
    let prevCount = 0;
    const lower = companyName.toLowerCase();
    for (const [name, count] of Object.entries(supplierCounts)) {
        if (name.toLowerCase() === lower) {
            prevCount = count;
            break;
        }
    }

    const nextTx = prevCount + 1;
    const prefix = extractCompanyPrefix(companyName);
    const yyyy = new Date().getFullYear();
    const padNum = String(nextTx).padStart(4, '0');
    const generatedFaktur = `SJ-${yyyy}/${prefix}/${padNum}`;

    fakturInput.value = generatedFaktur;
    fakturInput.dataset.autoGenerated = 'true';

    if (badge) {
        badge.textContent = `Transaksi ke-${nextTx}`;
        badge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle py-0.5 px-1.5';
    }
    if (helper) {
        helper.innerHTML = `<span class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Transaksi ke-${nextTx} dari ${companyName} ke Desiteks</span>`;
    }

    // Efek highlight input saat nomor terupdate otomatis
    fakturInput.style.transition = 'all 0.25s ease';
    fakturInput.style.backgroundColor = '#ecfdf5';
    fakturInput.style.borderColor = '#10b981';
    setTimeout(() => {
        fakturInput.style.backgroundColor = '';
        fakturInput.style.borderColor = '';
    }, 450);
}
</script>

@include('partials.camera-modal')
@endsection
