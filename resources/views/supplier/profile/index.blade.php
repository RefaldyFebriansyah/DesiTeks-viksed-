@extends('layouts.supplier')

@section('title', 'Profil Perusahaan Supplier')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Profil Perusahaan Supplier</h4>
        <p class="text-muted small mb-0">Kelola identitas perusahaan mitra, penanggung jawab (PIC), dan keamanan akun</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Mohon perbaiki data berikut:</div>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Form Data Profil -->
        <div class="col-lg-8">
            <div class="sup-card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-building me-2"></i>Informasi Identitas & Kontak Perusahaan
                    </h5>
                    <span class="badge bg-light text-secondary border">Kode: {{ $supplier->kode_supplier }}</span>
                </div>

                <form action="{{ route('supplier.profile.update') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nama Perusahaan / Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="nama_supplier" class="form-control" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nama Penanggung Jawab (PIC) <span class="text-danger">*</span></label>
                            <input type="text" name="pic_name" class="form-control" value="{{ old('pic_name', $user->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email Resmi Perusahaan <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email ?? $user->email) }}" required>
                            <div class="form-text" style="font-size: 11px;">Digunakan untuk menerima notifikasi dan login.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">No. Telepon / WhatsApp (+62)</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold bg-light text-navy border-end-0">+62</span>
                                <input type="text" name="no_telepon" class="form-control border-start-0" value="{{ old('no_telepon', preg_replace('/^\+62/', '', $supplier->no_telepon ?? '')) }}" placeholder="81234567890">
                            </div>
                            <div class="form-text" style="font-size: 11px;">Otomatis tersimpan dalam format internasional +62.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Asal Kota</label>
                            <input type="text" name="asal_kota" class="form-control" value="{{ old('asal_kota', $supplier->asal_kota) }}" placeholder="Contoh: Bandung, Solo, Pekalongan">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Username Akun</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->username }}" readonly>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Alamat Lengkap Perusahaan / Gudang Pusat</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap perusahaan supplier...">{{ old('alamat', $supplier->alamat) }}</textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="sup-btn-primary">
                            <i class="bi bi-floppy me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ganti Password -->
            <div class="sup-card p-4">
                <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary">
                    <i class="bi bi-shield-lock me-2"></i>Ubah Password Akun
                </h5>

                <form action="{{ route('supplier.profile.updatePassword') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Password Saat Ini <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" required placeholder="Password saat ini">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Ulangi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Ketik ulang password baru">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-outline-primary fw-semibold px-4">
                            <i class="bi bi-key me-1"></i> Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="sup-card p-4 mb-4 text-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3 d-flex align-items-center justify-content-center fw-bold fs-2" style="width: 80px; height: 80px;">
                    {{ strtoupper(substr($supplier->nama_supplier, 0, 2)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $supplier->nama_supplier }}</h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 mb-3">
                    <i class="bi bi-check-circle-fill me-1"></i> Rekanan Terverifikasi
                </span>
                <p class="text-muted small mb-0">
                    Akun ini bertindak sebagai mitra resmi penyuplai kain untuk seluruh cabang toko kain DesiTeks.
                </p>
            </div>

            <div class="sup-card p-4">
                <h6 class="fw-bold mb-3 pb-2 border-bottom">Ringkasan Kemitraan</h6>
                <div class="d-flex justify-content-between py-2 border-bottom small">
                    <span class="text-muted">Kode Rekanan</span>
                    <span class="fw-bold">{{ $supplier->kode_supplier }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom small">
                    <span class="text-muted">Total Surat Jalan</span>
                    <span class="fw-bold">{{ $supplier->deliveryOrders()->count() }} Dokumen</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom small">
                    <span class="text-muted">Pengiriman Berhasil</span>
                    <span class="fw-bold text-success">{{ $supplier->deliveryOrders()->where('status', 'diterima')->count() }} Diterima</span>
                </div>
                <div class="d-flex justify-content-between py-2 small">
                    <span class="text-muted">Mitra Sejak</span>
                    <span class="fw-semibold">{{ $supplier->created_at ? $supplier->created_at->format('d M Y') : '2026' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
