@extends('layouts.app')
@section('title', 'Tambah Barang Masuk')
@section('page-title', 'Catat Barang Masuk (All-In-One)')

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
    <form method="POST" action="{{ route('gudang.incoming-goods.store') }}" id="incomingForm">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="dt-form-group">
                    <label class="dt-label">Nomor Faktur / Surat Jalan <span class="required">*</span></label>
                    <input type="text" name="nomor_faktur" class="dt-input @error('nomor_faktur') is-invalid @enderror" value="{{ old('nomor_faktur') }}" required placeholder="Contoh: FAK-2024-001">
                    @error('nomor_faktur') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="dt-form-group">
                    <label class="dt-label">Nama Supplier / PT Pengirim <span class="required">*</span></label>
                    <input type="text" name="nama_supplier" list="supplierList" class="dt-input @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier') }}" required placeholder="Ketik/Pilih PT Supplier (misal: PT. Tekstil Nusantara)" autocomplete="off">
                    <datalist id="supplierList">
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->nama_supplier }}"></option>
                        @endforeach
                    </datalist>
                    @error('nama_supplier') <div class="dt-error-msg">{{ $message }}</div> @enderror
                    <small class="text-muted d-block mt-1">💡 Jika nama PT baru diketik, otomatis tersimpan sebagai Supplier baru.</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dt-form-group">
                    <label class="dt-label">Tanggal Masuk <span class="required">*</span></label>
                    <input type="date" name="tanggal" class="dt-input @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal') <div class="dt-error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-12">
                <div class="dt-form-group">
                    <label class="dt-label">Catatan / Keterangan</label>
                    <input type="text" name="catatan" class="dt-input" value="{{ old('catatan') }}" placeholder="Misal: Pengiriman stok baru">
                </div>
            </div>
        </div>

        <h5 class="fw-700 text-navy mb-3 d-flex justify-content-between align-items-center">
            <span>Daftar Item Kain Masuk</span>
            <button type="button" class="dt-btn dt-btn-gold dt-btn-xs" onclick="addItemRow()">
                <i class="bi bi-plus-lg"></i> Tambah Item Kain
            </button>
        </h5>

        <div class="dt-table-wrap mb-4">
            <table class="dt-table" id="itemTable">
                <thead>
                    <tr>
                        <th style="width: 35%">Pilih / Buat Kain</th>
                        <th style="width: 12%">Jumlah Rol</th>
                        <th style="width: 15%">Jumlah Meter</th>
                        <th style="width: 18%">Harga Beli/Meter (Rp)</th>
                        <th style="width: 10%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="itemContainer">
                    {{-- Default Row 0 --}}
                    <tr class="item-row" id="row_0">
                        <td colspan="5">
                            <div class="p-2 border rounded bg-light mb-2">
                                <div class="row g-2 align-items-center mb-2">
                                    <div class="col-md-8">
                                        <label class="dt-label mb-1">Pilih Kain</label>
                                        <select name="items[0][fabric_id]" class="dt-select fabric-select" onchange="toggleNewFabricFields(0, this.value)" required>
                                            <option value="">-- Pilih Kain Existing --</option>
                                            <option value="new" style="font-weight:700;color:var(--dt-gold)">➕ KAIN BARU (INPUT DETAIL BAWAH)</option>
                                            @foreach($fabrics as $f)
                                                <option value="{{ $f->id }}">{{ $f->kode_kain }} - {{ $f->nama_kain }} ({{ $f->category->nama_kategori ?? '-' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 text-end mt-4">
                                        <button type="button" class="dt-btn dt-btn-danger dt-btn-xs" onclick="removeItemRow(0)">Hapus Row</button>
                                    </div>
                                </div>

                                {{-- Input Fields for NEW Fabric (hidden by default) --}}
                                <div id="new_fabric_fields_0" class="row g-2 mt-2 pt-2 border-top" style="display:none;">
                                    <div class="col-12"><small class="fw-700 text-navy">📝 Detail Kain Baru:</small></div>
                                    <div class="col-md-4">
                                        <input type="text" name="items[0][nama_kain]" class="dt-input" placeholder="Nama Kain (misal: Cotton Combed White)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="items[0][nama_kategori]" list="categoryList" class="dt-input" placeholder="Kategori (misal: Cotton, Linen)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="items[0][warna]" class="dt-input" placeholder="Warna (misal: Putih, Merah)">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" step="0.01" name="items[0][harga_per_meter]" class="dt-input" placeholder="Harga Jual per Meter (Rp)">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" step="0.01" name="items[0][harga_per_rol]" class="dt-input" placeholder="Harga Jual per Rol (Rp)">
                                    </div>
                                </div>

                                {{-- Quantities --}}
                                <div class="row g-2 mt-2 pt-2 border-top">
                                    <div class="col-md-4">
                                        <label class="dt-label mb-1">Jumlah Rol</label>
                                        <input type="number" name="items[0][jumlah_rol]" class="dt-input" value="0" min="0" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="dt-label mb-1">Jumlah Meter</label>
                                        <input type="number" step="0.01" name="items[0][jumlah_meter]" class="dt-input" value="0" min="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="dt-label mb-1">Harga Beli / Meter (Rp)</label>
                                        <input type="number" step="0.01" name="items[0][harga_beli]" class="dt-input" value="0" min="0" required>
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
            <button type="submit" class="dt-btn dt-btn-primary"><i class="bi bi-check-circle me-1"></i> Simpan Barang Masuk</button>
        </div>
    </form>
</div>

<script>
let itemIndex = 1;

function addItemRow() {
    const container = document.getElementById('itemContainer');
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.id = `row_${itemIndex}`;
    const idx = itemIndex;

    tr.innerHTML = `
        <td colspan="5">
            <div class="p-2 border rounded bg-light mb-2">
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-md-8">
                        <label class="dt-label mb-1">Pilih Kain</label>
                        <select name="items[${idx}][fabric_id]" class="dt-select fabric-select" onchange="toggleNewFabricFields(${idx}, this.value)" required>
                            <option value="">-- Pilih Kain Existing --</option>
                            <option value="new" style="font-weight:700;color:var(--dt-gold)">➕ KAIN BARU (INPUT DETAIL BAWAH)</option>
                            @foreach($fabrics as $f)
                                <option value="{{ $f->id }}">{{ $f->kode_kain }} - {{ $f->nama_kain }} ({{ $f->category->nama_kategori ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 text-end mt-4">
                        <button type="button" class="dt-btn dt-btn-danger dt-btn-xs" onclick="removeItemRow(${idx})">Hapus Row</button>
                    </div>
                </div>

                <div id="new_fabric_fields_${idx}" class="row g-2 mt-2 pt-2 border-top" style="display:none;">
                    <div class="col-12"><small class="fw-700 text-navy">📝 Detail Kain Baru:</small></div>
                    <div class="col-md-4">
                        <input type="text" name="items[${idx}][nama_kain]" class="dt-input" placeholder="Nama Kain (misal: Cotton Combed White)">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="items[${idx}][nama_kategori]" list="categoryList" class="dt-input" placeholder="Kategori (misal: Cotton, Linen)">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="items[${idx}][warna]" class="dt-input" placeholder="Warna (misal: Putih, Merah)">
                    </div>
                    <div class="col-md-6">
                        <input type="number" step="0.01" name="items[${idx}][harga_per_meter]" class="dt-input" placeholder="Harga Jual per Meter (Rp)">
                    </div>
                    <div class="col-md-6">
                        <input type="number" step="0.01" name="items[${idx}][harga_per_rol]" class="dt-input" placeholder="Harga Jual per Rol (Rp)">
                    </div>
                </div>

                <div class="row g-2 mt-2 pt-2 border-top">
                    <div class="col-md-4">
                        <label class="dt-label mb-1">Jumlah Rol</label>
                        <input type="number" name="items[${idx}][jumlah_rol]" class="dt-input" value="0" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="dt-label mb-1">Jumlah Meter</label>
                        <input type="number" step="0.01" name="items[${idx}][jumlah_meter]" class="dt-input" value="0" min="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label class="dt-label mb-1">Harga Beli / Meter (Rp)</label>
                        <input type="number" step="0.01" name="items[${idx}][harga_beli]" class="dt-input" value="0" min="0" required>
                    </div>
                </div>
            </div>
        </td>
    `;
    container.appendChild(tr);
    itemIndex++;
}

function toggleNewFabricFields(idx, val) {
    const fields = document.getElementById(`new_fabric_fields_${idx}`);
    if (val === 'new') {
        fields.style.display = 'flex';
        fields.querySelectorAll('input').forEach(i => i.required = true);
    } else {
        fields.style.display = 'none';
        fields.querySelectorAll('input').forEach(i => i.required = false);
    }
}

function removeItemRow(idx) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        const row = document.getElementById(`row_${idx}`);
        if (row) row.remove();
    } else {
        alert('Minimal harus ada 1 item kain masuk.');
    }
}
</script>
@endsection
