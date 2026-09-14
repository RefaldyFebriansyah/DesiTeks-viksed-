@extends('layouts.supplier')

@section('title', 'Buat Surat Jalan Online')

@push('styles')
<style>
    .form-section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
        transition: all 0.2s ease;
    }
    .form-section-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06);
    }
    .card-header-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 9px 14px;
        font-size: 13.5px;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }
    .upload-box-custom {
        border: 2px dashed #cbd5e1 !important;
        border-radius: 14px;
        background-color: #f8fafc;
        transition: all 0.2s ease;
    }
    .upload-box-custom:hover {
        border-color: #3b82f6 !important;
        background-color: #eff6ff;
    }
    /* Sembunyikan spinner panah pada input number agar angka tidak terpotong */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none !important; 
        margin: 0 !important; 
    }
    input[type=number] {
        -moz-appearance: textfield !important;
    }
    .table-custom-items .form-control-sm {
        padding: 5px 8px !important;
        font-size: 13px !important;
    }
    .table-custom-items thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 14px;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom-items tbody td {
        padding: 10px 12px;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div>
    <!-- Back & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('supplier.delivery-orders.index') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center mb-1 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Surat Jalan
            </a>
            <h3 class="fw-bold mb-0 text-dark">Buat Surat Jalan Online</h3>
            <p class="text-muted small mb-0">Terbitkan dokumen pengiriman barang resmi dari <strong>{{ $supplier->nama_supplier }}</strong> menuju Gudang DesiTeks</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius:12px;">
            <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan pada input form:</div>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('supplier.delivery-orders.store') }}" method="POST" enctype="multipart/form-data" id="formSuratJalan">
        @csrf

        <div class="row g-4 mb-4">
            <!-- Info Pengiriman -->
            <div class="col-lg-7">
                <div class="form-section-card p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div class="card-header-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Informasi Surat Jalan & Tujuan</h6>
                            <span class="text-muted small">Nomor dokumen, tanggal, dan cabang gudang penerima</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nomor Surat Jalan <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_surat_jalan" class="form-control fw-semibold" value="{{ old('nomor_surat_jalan', $autoNomor) }}" required>
                            <div class="form-text" style="font-size: 11px;">Otomatis dihasilkan sistem, atau ganti dengan no. internal Anda.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Tanggal Pengiriman <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kirim" class="form-control fw-semibold" value="{{ old('tanggal_kirim', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Gudang / Cabang Tujuan <span class="text-danger">*</span></label>
                            @if($branches->count() <= 1)
                                @php $targetBranch = $branches->first(); @endphp
                                <input type="hidden" name="branch_id" value="{{ $targetBranch?->id ?? 1 }}">
                                <input type="text" class="form-control bg-light fw-bold text-dark" value="{{ $targetBranch?->nama_cabang ?? 'Cabang Utama (Pusat)' }} — {{ $targetBranch?->alamat ?? 'Bandung' }}" readonly>
                            @else
                                <select name="branch_id" class="form-select fw-semibold" required>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ (old('branch_id') == $branch->id || $loop->first) ? 'selected' : '' }}>
                                            {{ $branch->nama_cabang }} {{ $branch->kota ? "($branch->kota)" : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Perusahaan Pengirim (Supplier)</label>
                            <input type="text" class="form-control bg-light fw-bold text-secondary" value="{{ $supplier->nama_supplier }}" readonly>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Catatan / Keterangan Pengiriman</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan instruksi khusus pengiriman atau informasi pesanan...">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Ekspedisi & Supir + Foto Bukti -->
            <div class="col-lg-5">
                <div class="form-section-card p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div class="card-header-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Informasi Supir & Armada</h6>
                            <span class="text-muted small">Detail supir pengantar, nomor kendaraan, & foto bukti</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Nama Ekspedisi / Jenis Armada</label>
                            <input type="text" name="ekspedisi" class="form-control" value="{{ old('ekspedisi') }}" placeholder="Contoh: Truk Box, Lalamove, Kendaraan Sendiri">
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-dark">Nama Supir / Pengemudi</label>
                            <input type="text" name="nama_supir" class="form-control" value="{{ old('nama_supir') }}" placeholder="e.g. Supriadi">
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-dark">Nomor Plat Kendaraan</label>
                            <input type="text" name="plat_nomor" class="form-control" value="{{ old('plat_nomor') }}" placeholder="e.g. B 1234 JK">
                        </div>

                        <!-- Foto Bukti Supir / Pengiriman Custom Upload Dropzone -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">
                                <i class="bi bi-camera-fill text-primary me-1"></i> Foto Bukti Kirim (Foto Supir / Armada / Surat Jalan)
                            </label>
                            <div class="upload-box-custom p-3 text-center cursor-pointer" onclick="document.getElementById('fotoSuratJalanInput').click()" id="uploadDropArea" style="cursor: pointer;">
                                <input type="file" name="foto_surat_jalan" id="fotoSuratJalanInput" class="d-none" accept="image/*" onchange="previewDriverPhoto(this)">
                                <div id="uploadPlaceholder">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-camera fs-4"></i>
                                    </div>
                                    <div class="fw-semibold text-dark small mb-1">Unggah Foto Supir / Bukti Pengiriman</div>
                                    <div class="text-muted" style="font-size: 11px;">Format JPG, PNG (Maks 4MB). Memudahkan staf gudang verifikasi.</div>
                                </div>
                                <div id="uploadPreview" class="d-none">
                                    <img id="imgPreviewTag" src="" alt="Preview Bukti Kirim" class="img-thumbnail mb-2" style="max-height: 130px; object-fit: contain;">
                                    <div class="small fw-semibold text-success"><i class="bi bi-check-circle-fill me-1"></i> Foto berhasil dipilih</div>
                                    <div class="text-muted" style="font-size: 11px;">Klik di sini jika ingin mengganti foto</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Item Kain -->
        <div class="form-section-card p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="card-header-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-layers-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Rincian Kain yang Dikirim</h6>
                        <span class="text-muted small"><strong>Per 1 Rol = 50 Meter</strong> (Otomatis menghitung total meteran saat Anda memasukkan jumlah rol)</span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom-items align-middle" id="itemsTable" style="font-size: 13.5px;">
                    <thead>
                        <tr class="text-center align-middle">
                            <th style="width: 45px;">#</th>
                            <th style="min-width: 210px;" class="text-start">Nama / Pilihan Kain <span class="text-danger">*</span></th>
                            <th style="width: 120px;">Jenis / Bahan</th>
                            <th style="width: 110px;">Warna</th>
                            <th style="width: 120px;">Jml Rol <span class="text-danger">*</span></th>
                            <th style="width: 135px;">Total Meter <span class="text-danger">*</span></th>
                            <th style="width: 175px;">Harga Satuan (Rp)</th>
                            <th style="width: 165px;" class="text-end text-nowrap">Subtotal (Rp)</th>
                            <th style="width: 45px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <!-- Baris Item 0 -->
                        <tr class="item-row">
                            <td class="text-center row-num fw-bold text-muted">1</td>
                            <td>
                                <input type="text" name="items[0][nama_kain]" class="form-control form-control-sm nama-kain-input" placeholder="Ketik / pilih nama kain..." required list="fabricsList" oninput="onSelectFabric(this, 0)">
                                <input type="hidden" name="items[0][fabric_id]" class="fabric-id-input">
                            </td>
                            <td>
                                <input type="text" name="items[0][jenis_kain]" class="form-control form-control-sm jenis-kain-input" placeholder="e.g. Katun">
                            </td>
                            <td>
                                <input type="text" name="items[0][warna]" class="form-control form-control-sm warna-input" placeholder="e.g. Putih">
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="items[0][jumlah_rol]" class="form-control form-control-sm text-center rol-input fw-bold" min="1" value="1" required oninput="updateRowMeter(this)">
                                    <span class="input-group-text bg-light text-muted" style="font-size:11px;">Rol</span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="items[0][jumlah_meter]" class="form-control form-control-sm text-center meter-input fw-bold text-primary" min="0.1" value="50" required oninput="calculateTotals()">
                                    <span class="input-group-text bg-primary bg-opacity-10 text-primary fw-bold" style="font-size:11px;">m</span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted px-2" style="font-size:11px;">Rp</span>
                                    <input type="number" step="0.01" name="items[0][harga_satuan]" class="form-control form-control-sm text-end harga-input" min="0" placeholder="0" oninput="calculateTotals()">
                                </div>
                            </td>
                            <td class="text-end fw-bold text-dark subtotal-display text-nowrap" style="white-space: nowrap;">Rp 0</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 border-0" onclick="removeItemRow(this)" title="Hapus Baris">
                                    <i class="bi bi-trash fs-6"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-light fw-bold border-top border-2 align-middle">
                            <td colspan="4" class="text-end py-3 text-dark fs-6 text-nowrap">TOTAL KESELURUHAN PENGIRIMAN:</td>
                            <td class="text-center py-3 text-primary fs-6 text-nowrap" id="grandTotalRol">1 Rol</td>
                            <td class="text-center py-3 text-primary fs-6 text-nowrap" id="grandTotalMeter">50 m</td>
                            <td class="text-end py-3 text-muted small text-nowrap" style="white-space: nowrap;">Estimasi Nilai:</td>
                            <td class="text-end py-3 text-success fs-5 text-nowrap" id="grandTotalNominal" style="white-space: nowrap;">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Action Controls (Tambah Baris, Batal, Terbitkan & Kirim) -->
            <hr class="my-4 border-secondary border-opacity-25">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-1">
                <button type="button" class="btn btn-outline-primary fw-bold px-3.5 py-2 rounded-pill d-inline-flex align-items-center gap-2" onclick="addItemRow()">
                    <i class="bi bi-plus-lg fs-6"></i>
                    <span>Tambah Baris Kain</span>
                </button>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <a href="{{ route('supplier.delivery-orders.index') }}" class="btn btn-light border px-4 py-2 rounded-pill fw-semibold text-secondary" style="font-size: 14px;">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="font-size: 14px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                        <i class="bi bi-send-fill"></i>
                        <span>Terbitkan & Kirim Surat Jalan</span>
                    </button>
                </div>
            </div>

            <!-- Autocomplete list kain -->
            <datalist id="fabricsList">
                @foreach($fabrics as $fabric)
                    <option value="{{ $fabric->nama_kain }}" data-id="{{ $fabric->id }}" data-jenis="{{ $fabric->jenis_kain }}" data-warna="{{ $fabric->warna }}" data-meter="{{ $fabric->meter_per_rol }}">
                        {{ $fabric->nama_kain }} ({{ $fabric->warna ?? '-' }})
                    </option>
                @endforeach
            </datalist>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const fabricsData = [
        @foreach($fabrics as $f)
        {
            id: {{ $f->id }},
            nama: {!! json_encode($f->nama_kain) !!},
            jenis: {!! json_encode($f->jenis_kain) !!},
            warna: {!! json_encode($f->warna) !!},
            meter_per_rol: {{ $f->meter_per_rol ?: 50 }}
        },
        @endforeach
    ];

    let rowIndex = 1;

    function previewDriverPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imgPreviewTag').src = e.target.result;
                document.getElementById('uploadPlaceholder').classList.add('d-none');
                document.getElementById('uploadPreview').classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function onSelectFabric(input, idx) {
        const val = input.value.trim().toLowerCase();
        const row = input.closest('tr');
        const match = fabricsData.find(f => f.nama.toLowerCase() === val);

        if (match) {
            row.querySelector('.fabric-id-input').value = match.id;
            if (!row.querySelector('.jenis-kain-input').value) {
                row.querySelector('.jenis-kain-input').value = match.jenis || '';
            }
            if (!row.querySelector('.warna-input').value) {
                row.querySelector('.warna-input').value = match.warna || '';
            }
        } else {
            row.querySelector('.fabric-id-input').value = '';
        }
    }

    function updateRowMeter(input) {
        const row = input.closest('tr');
        const rol = parseFloat(input.value) || 0;
        const meterInput = row.querySelector('.meter-input');
        // Otomatis 1 rol = 50 meter
        meterInput.value = (rol * 50);
        calculateTotals();
    }

    function addItemRow() {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="text-center row-num fw-bold text-muted">${tbody.children.length + 1}</td>
            <td>
                <input type="text" name="items[${rowIndex}][nama_kain]" class="form-control form-control-sm nama-kain-input" placeholder="Ketik / pilih nama kain..." required list="fabricsList" oninput="onSelectFabric(this, ${rowIndex})">
                <input type="hidden" name="items[${rowIndex}][fabric_id]" class="fabric-id-input">
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][jenis_kain]" class="form-control form-control-sm jenis-kain-input" placeholder="e.g. Katun">
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][warna]" class="form-control form-control-sm warna-input" placeholder="e.g. Putih">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="items[${rowIndex}][jumlah_rol]" class="form-control form-control-sm text-center rol-input fw-bold" min="1" value="1" required oninput="updateRowMeter(this)">
                    <span class="input-group-text bg-light text-muted" style="font-size:11px;">Rol</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" step="0.01" name="items[${rowIndex}][jumlah_meter]" class="form-control form-control-sm text-center meter-input fw-bold text-primary" min="0.1" value="50" required oninput="calculateTotals()">
                    <span class="input-group-text bg-primary bg-opacity-10 text-primary fw-bold" style="font-size:11px;">m</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted px-2" style="font-size:11px;">Rp</span>
                    <input type="number" step="0.01" name="items[${rowIndex}][harga_satuan]" class="form-control form-control-sm text-end harga-input" min="0" placeholder="0" oninput="calculateTotals()">
                </div>
            </td>
            <td class="text-end fw-bold text-dark subtotal-display text-nowrap" style="white-space: nowrap;">Rp 0</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-link text-danger p-0 border-0" onclick="removeItemRow(this)" title="Hapus Baris">
                    <i class="bi bi-trash fs-6"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        calculateTotals();
    }

    function removeItemRow(btn) {
        const tbody = document.getElementById('itemsBody');
        if (tbody.children.length <= 1) {
            alert('Minimal harus ada 1 item kain dalam surat jalan.');
            return;
        }
        btn.closest('tr').remove();
        Array.from(tbody.children).forEach((row, i) => {
            row.querySelector('.row-num').innerText = i + 1;
        });
        calculateTotals();
    }

    function calculateTotals() {
        let totalRol = 0;
        let totalMeter = 0;
        let totalNominal = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const rol = parseFloat(row.querySelector('.rol-input').value) || 0;
            const meter = parseFloat(row.querySelector('.meter-input').value) || 0;
            const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
            const subtotal = meter * harga;

            row.querySelector('.subtotal-display').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');

            totalRol += rol;
            totalMeter += meter;
            totalNominal += subtotal;
        });

        document.getElementById('grandTotalRol').innerText = totalRol + ' Rol';
        document.getElementById('grandTotalMeter').innerText = totalMeter.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 }) + ' m';
        document.getElementById('grandTotalNominal').innerText = 'Rp ' + totalNominal.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', calculateTotals);
</script>
@endpush
