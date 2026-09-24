<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Fabric;
use App\Models\IncomingGood;
use App\Models\IncomingGoodsDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class NewSuppliersAndIncomingGoodsSeeder extends Seeder
{
    public function run(): void
    {
        $mainBranch = Branch::where('is_main', true)->first() ?? Branch::first();
        $gudangUser = User::where('role', 'gudang')->first() ?? User::first();

        // Pastikan folder penyimpanan lampiran surat jalan ada
        $storageDir = storage_path('app/public/incoming_goods');
        $publicStorageDir = public_path('storage/incoming_goods');
        if (!File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }
        if (!File::exists($publicStorageDir)) {
            File::makeDirectory($publicStorageDir, 0755, true);
        }

        $items = [
            [
                'supplier' => [
                    'kode_supplier' => 'SUP004',
                    'nama_supplier' => 'PT. Sinar Sutra Abadi',
                    'email'         => 'sales@sinarsutra.co.id',
                    'asal_kota'     => 'Pekalongan',
                    'alamat'        => 'Jl. Urip Sumoharjo No. 88, Pekalongan, Jawa Tengah',
                    'no_telepon'    => '+62285421888',
                ],
                'fabric' => [
                    'kode_kain'       => 'SUT-002',
                    'nama_kain'       => 'Sutra Shantung Twill Champagne',
                    'category_name'   => 'Sutra',
                    'jenis_kain'      => 'Sutra Shantung',
                    'warna'           => 'Champagne Gold',
                    'motif'           => 'Polos Shantung Slub',
                    'harga_per_meter' => 85000,
                    'harga_per_rol'   => 3900000,
                    'meter_per_rol'   => 50.00,
                    'stok_minimum'    => 15,
                    'status'          => 'aktif',
                ],
                'incoming' => [
                    'nomor_faktur'    => 'SJ-2026/SSA/0905-01',
                    'tanggal'         => '2026-09-05',
                    'total_rol'       => 15,
                    'total_meter'     => 750.00,
                    'harga_beli'      => 65000,
                    'catatan'         => 'Pengiriman 15 roll Sutra Shantung Twill Champagne via Ekspedisi Laris Cargo. Kondisi kemasan utuh bersegel pabrik.',
                    'sopir'           => 'Bambang Supriadi',
                    'no_polisi'       => 'G 8923 LK',
                ],
            ],
            [
                'supplier' => [
                    'kode_supplier' => 'SUP005',
                    'nama_supplier' => 'CV. Mitra Denim Prima',
                    'email'         => 'order@mitradenim.com',
                    'asal_kota'     => 'Surabaya',
                    'alamat'        => 'Kawasan Industri SIER Blok B-14, Surabaya, Jawa Timur',
                    'no_telepon'    => '+62318432190',
                ],
                'fabric' => [
                    'kode_kain'       => 'DNM-002',
                    'nama_kain'       => 'Denim Selvedge Raw 14oz',
                    'category_name'   => 'Denim',
                    'jenis_kain'      => 'Denim Selvedge',
                    'warna'           => 'Deep Indigo',
                    'motif'           => 'Polos Kaku',
                    'harga_per_meter' => 68000,
                    'harga_per_rol'   => 3100000,
                    'meter_per_rol'   => 50.00,
                    'stok_minimum'    => 15,
                    'status'          => 'aktif',
                ],
                'incoming' => [
                    'nomor_faktur'    => 'SJ-2026/MDP/0905-02',
                    'tanggal'         => '2026-09-05',
                    'total_rol'       => 15,
                    'total_meter'     => 750.00,
                    'harga_beli'      => 50000,
                    'catatan'         => 'Pengiriman 15 roll Denim Selvedge Raw 14oz. Armada Truk L 9421 KDA.',
                    'sopir'           => 'Hadi Gunawan',
                    'no_polisi'       => 'L 9421 KDA',
                ],
            ],
            [
                'supplier' => [
                    'kode_supplier' => 'SUP006',
                    'nama_supplier' => 'PT. Gajah Indah Textile',
                    'email'         => 'sales@gajahindahtextile.id',
                    'asal_kota'     => 'Solo',
                    'alamat'        => 'Jl. Slamet Riyadi No. 340, Surakarta, Jawa Tengah',
                    'no_telepon'    => '+62271645220',
                ],
                'fabric' => [
                    'kode_kain'       => 'CTN-003',
                    'nama_kain'       => 'Cotton Madinah Fancy Sage',
                    'category_name'   => 'Cotton',
                    'jenis_kain'      => 'Cotton Madinah',
                    'warna'           => 'Sage Green',
                    'motif'           => 'Two-Tone Slub',
                    'harga_per_meter' => 42000,
                    'harga_per_rol'   => 1900000,
                    'meter_per_rol'   => 50.00,
                    'stok_minimum'    => 20,
                    'status'          => 'aktif',
                ],
                'incoming' => [
                    'nomor_faktur'    => 'SJ-2026/GIT/0905-03',
                    'tanggal'         => '2026-09-05',
                    'total_rol'       => 15,
                    'total_meter'     => 750.00,
                    'harga_beli'      => 30000,
                    'catatan'         => 'Pengiriman Cotton Madinah Fancy Sage 15 roll gulungan pabrik.',
                    'sopir'           => 'Slamet Riyanto',
                    'no_polisi'       => 'AD 1450 BT',
                ],
            ],
            [
                'supplier' => [
                    'kode_supplier' => 'SUP007',
                    'nama_supplier' => 'CV. Megah Jaya Brokat',
                    'email'         => 'cs@megahjayabrokat.com',
                    'asal_kota'     => 'Semarang',
                    'alamat'        => 'Jl. Pemuda No. 122, Semarang, Jawa Tengah',
                    'no_telepon'    => '+62243548901',
                ],
                'fabric' => [
                    'kode_kain'       => 'BKT-002',
                    'nama_kain'       => 'Brokat Cornelli Gliter Mutiara',
                    'category_name'   => 'Brokat',
                    'jenis_kain'      => 'Brokat Cornelli',
                    'warna'           => 'Silver Grey',
                    'motif'           => 'Bunga Cornelli Gliter',
                    'harga_per_meter' => 110000,
                    'harga_per_rol'   => 5000000,
                    'meter_per_rol'   => 50.00,
                    'stok_minimum'    => 10,
                    'status'          => 'aktif',
                ],
                'incoming' => [
                    'nomor_faktur'    => 'SJ-2026/MJB/0905-04',
                    'tanggal'         => '2026-09-05',
                    'total_rol'       => 15,
                    'total_meter'     => 750.00,
                    'harga_beli'      => 80000,
                    'catatan'         => 'Pengiriman Brokat Cornelli Gliter Mutiara 15 roll kemasan plastik tebal ganda.',
                    'sopir'           => 'Agus Prasetyo',
                    'no_polisi'       => 'H 8129 DA',
                ],
            ],
            [
                'supplier' => [
                    'kode_supplier' => 'SUP008',
                    'nama_supplier' => 'PT. Prima Woolen Indo',
                    'email'         => 'info@primawoolen.co.id',
                    'asal_kota'     => 'Tangerang',
                    'alamat'        => 'Kawasan Industri Manis Jl. Manis Raya No. 45, Tangerang, Banten',
                    'no_telepon'    => '+62215918822',
                ],
                'fabric' => [
                    'kode_kain'       => 'WLP-002',
                    'nama_kain'       => 'Woolpeach Exclusive Jet Charcoal',
                    'category_name'   => 'Woolpeach',
                    'jenis_kain'      => 'Woolpeach Premium',
                    'warna'           => 'Charcoal Grey',
                    'motif'           => 'Polos Soft Drape',
                    'harga_per_meter' => 45000,
                    'harga_per_rol'   => 2050000,
                    'meter_per_rol'   => 50.00,
                    'stok_minimum'    => 15,
                    'status'          => 'aktif',
                ],
                'incoming' => [
                    'nomor_faktur'    => 'SJ-2026/PWI/0905-05',
                    'tanggal'         => '2026-09-05',
                    'total_rol'       => 15,
                    'total_meter'     => 750.00,
                    'harga_beli'      => 33000,
                    'catatan'         => 'Pengiriman Woolpeach Exclusive Jet Charcoal 15 roll.',
                    'sopir'           => 'Wahyu Hidayat',
                    'no_polisi'       => 'B 9042 CX',
                ],
            ],
        ];

        foreach ($items as $entry) {
            // 1. Supplier
            $supplier = Supplier::updateOrCreate(
                ['kode_supplier' => $entry['supplier']['kode_supplier']],
                $entry['supplier']
            );

            // 2. Kategori & Fabric
            $category = Category::firstOrCreate(['nama_kategori' => $entry['fabric']['category_name']]);
            $fabricData = $entry['fabric'];
            unset($fabricData['category_name']);
            $fabricData['category_id'] = $category->id;

            $fabric = Fabric::updateOrCreate(
                ['kode_kain' => $fabricData['kode_kain']],
                $fabricData
            );

            // 3. Generate Foto Surat Jalan Resmi
            $fakturNo = $entry['incoming']['nomor_faktur'];
            $slugFaktur = preg_replace('/[^a-zA-Z0-9_-]/', '-', $fakturNo);
            $filename = "surat_jalan_{$slugFaktur}.png";
            $relativePath = "incoming_goods/{$filename}";
            $fullStoragePath = "{$storageDir}/{$filename}";
            $fullPublicPath  = "{$publicStorageDir}/{$filename}";

            $this->generateSuratJalanImage(
                $fullStoragePath,
                $supplier,
                $fabric,
                $entry['incoming'],
                $gudangUser
            );

            if (File::exists($fullStoragePath)) {
                File::copy($fullStoragePath, $fullPublicPath);
            }

            // 4. Incoming Good & Details
            $totalBeli = $entry['incoming']['total_meter'] * $entry['incoming']['harga_beli'];
            
            $incomingGood = IncomingGood::updateOrCreate(
                ['nomor_faktur' => $fakturNo],
                [
                    'supplier_id'     => $supplier->id,
                    'user_id'         => $gudangUser?->id ?? 1,
                    'branch_id'       => $mainBranch?->id ?? 1,
                    'tanggal'         => $entry['incoming']['tanggal'],
                    'catatan'         => $entry['incoming']['catatan'],
                    'foto_lampiran'   => $relativePath,
                    'total_pembelian' => $totalBeli,
                    'total_rol'       => $entry['incoming']['total_rol'],
                    'total_meter'     => $entry['incoming']['total_meter'],
                ]
            );

            IncomingGoodsDetail::updateOrCreate(
                [
                    'incoming_good_id' => $incomingGood->id,
                    'fabric_id'        => $fabric->id,
                ],
                [
                    'jumlah_rol'   => $entry['incoming']['total_rol'],
                    'jumlah_meter' => $entry['incoming']['total_meter'],
                    'harga_beli'   => $entry['incoming']['harga_beli'],
                    'subtotal'     => $totalBeli,
                ]
            );

            // 5. Stock (15 rol x 50m = 750m total)
            Stock::updateOrCreate(
                [
                    'fabric_id' => $fabric->id,
                    'branch_id' => $mainBranch?->id ?? 1,
                ],
                [
                    'stok_rol'   => $entry['incoming']['total_rol'],
                    'stok_meter' => 0.00,
                    'updated_at' => now(),
                ]
            );

            // 6. Stock Movement
            StockMovement::updateOrCreate(
                [
                    'reference_type' => 'App\Models\IncomingGood',
                    'reference_id'   => $incomingGood->id,
                    'fabric_id'      => $fabric->id,
                ],
                [
                    'user_id'      => $gudangUser?->id ?? 1,
                    'jenis'        => 'barang_masuk',
                    'jumlah_rol'   => $entry['incoming']['total_rol'],
                    'jumlah_meter' => $entry['incoming']['total_meter'],
                    'keterangan'   => "Penerimaan barang masuk 15 roll ({$fakturNo}) dari {$supplier->nama_supplier}",
                ]
            );

            // 7. Audit Log & Notification
            AuditLog::create([
                'user_id'   => $gudangUser?->id ?? 1,
                'aktivitas' => "Barang masuk: {$fakturNo} (15 roll / 750m) dari {$supplier->nama_supplier}",
                'model'     => 'IncomingGood',
                'model_id'  => $incomingGood->id,
            ]);

            AppNotification::create([
                'type'    => 'barang_masuk',
                'title'   => 'Barang Masuk Diterima',
                'message' => "Penerimaan faktur {$fakturNo} dari {$supplier->nama_supplier} (15 rol / 750.0m).",
                'link'    => route('gudang.incoming-goods.show', $incomingGood->id),
            ]);
        }
    }

    /**
     * Membuat gambar dokumen Surat Jalan resmi dengan PHP GD
     */
    private function generateSuratJalanImage(string $path, Supplier $supplier, Fabric $fabric, array $incoming, ?User $user): void
    {
        $w = 850;
        $h = 1100;
        $im = imagecreatetruecolor($w, $h);

        // Warna
        $bg         = imagecolorallocate($im, 255, 255, 255);
        $headerBg   = imagecolorallocate($im, 15, 23, 42);    // Navy #0f172a
        $textDark   = imagecolorallocate($im, 30, 41, 59);    // Slate #1e293b
        $textLight  = imagecolorallocate($im, 255, 255, 255);
        $grayText   = imagecolorallocate($im, 100, 116, 139); // Slate-500
        $gold       = imagecolorallocate($im, 201, 168, 76);  // DesiTeks gold
        $lineColor  = imagecolorallocate($im, 226, 232, 240); // Border #e2e8f0
        $boxBg      = imagecolorallocate($im, 248, 250, 252); // #f8fafc
        $stampGreen = imagecolorallocate($im, 16, 185, 129);  // Emerald

        imagefilledrectangle($im, 0, 0, $w, $h, $bg);

        // Header Banner
        imagefilledrectangle($im, 0, 0, $w, 110, $headerBg);
        imagefilledrectangle($im, 0, 105, $w, 110, $gold);

        imagestring($im, 5, 40, 25, strtoupper($supplier->nama_supplier), $textLight);
        imagestring($im, 3, 40, 48, $supplier->alamat . ' | Telp: ' . $supplier->no_telepon, $gold);
        imagestring($im, 3, 40, 68, 'Email: ' . $supplier->email . ' | Kode Mitra: ' . $supplier->kode_supplier, imagecolorallocate($im, 203, 213, 225));

        // Judul Dokumen
        $title = "SURAT JALAN / DELIVERY ORDER";
        imagestring($im, 5, 40, 135, $title, $headerBg);
        imagestring($im, 4, 40, 158, "Nomor: " . $incoming['nomor_faktur'], $gold);

        // Kotak Informasi Pengiriman
        imagefilledrectangle($im, 40, 190, 410, 310, $boxBg);
        imagerectangle($im, 40, 190, 410, 310, $lineColor);

        imagestring($im, 4, 55, 205, "PENGIRIM:", $headerBg);
        imagestring($im, 3, 55, 228, $supplier->nama_supplier, $textDark);
        imagestring($im, 2, 55, 248, "Kota Asal: " . $supplier->asal_kota, $grayText);
        imagestring($im, 2, 55, 265, "Armada: " . ($incoming['no_polisi'] ?? 'B 9231 TDA'), $grayText);
        imagestring($im, 2, 55, 282, "Pengemudi: " . ($incoming['sopir'] ?? 'Staff Ekspedisi'), $grayText);

        imagefilledrectangle($im, 440, 190, 810, 310, $boxBg);
        imagerectangle($im, 440, 190, 810, 310, $lineColor);

        imagestring($im, 4, 455, 205, "PENERIMA (TUJUAN):", $headerBg);
        imagestring($im, 3, 455, 228, "TOKO KAIN MITRASERATBUANA (PUSAT)", $textDark);
        imagestring($im, 2, 455, 248, "Jl. Tekstil Raya No. 101, Bandung", $grayText);
        imagestring($im, 2, 455, 265, "Tanggal Kirim: " . date('d F Y', strtotime($incoming['tanggal'])), $grayText);
        imagestring($im, 2, 455, 282, "Status: DITERIMA LENGKAP & UTUH", $stampGreen);

        // Tabel Rincian Barang
        $tY = 340;
        imagefilledrectangle($im, 40, $tY, 810, $tY + 35, $headerBg);
        imagestring($im, 3, 55, $tY + 10, "NO", $textLight);
        imagestring($im, 3, 95, $tY + 10, "KODE KAIN", $textLight);
        imagestring($im, 3, 210, $tY + 10, "DESKRIPSI KAIN / WARNA", $textLight);
        imagestring($im, 3, 520, $tY + 10, "QTY ROL", $textLight);
        imagestring($im, 3, 610, $tY + 10, "M/ROL", $textLight);
        imagestring($im, 3, 700, $tY + 10, "TOTAL METER", $textLight);

        // Baris Item
        $rY = $tY + 35;
        imagefilledrectangle($im, 40, $rY, 810, $rY + 50, $bg);
        imagerectangle($im, 40, $rY, 810, $rY + 50, $lineColor);

        imagestring($im, 3, 60, $rY + 16, "1", $textDark);
        imagestring($im, 3, 95, $rY + 16, $fabric->kode_kain, $textDark);
        imagestring($im, 3, 210, $rY + 8, $fabric->nama_kain, $headerBg);
        imagestring($im, 2, 210, $rY + 28, "Warna: {$fabric->warna} | Motif: {$fabric->motif}", $grayText);
        imagestring($im, 4, 530, $rY + 16, "15 Rol", $headerBg);
        imagestring($im, 3, 620, $rY + 16, "50.0 m", $textDark);
        imagestring($im, 4, 710, $rY + 16, "750.0 m", $headerBg);

        // Baris Total
        $totY = $rY + 50;
        imagefilledrectangle($im, 40, $totY, 810, $totY + 40, $boxBg);
        imagerectangle($im, 40, $totY, 810, $totY + 40, $lineColor);
        imagestring($im, 4, 300, $totY + 12, "TOTAL BARANG DIKIRIM:", $headerBg);
        imagestring($im, 4, 530, $totY + 12, "15 ROL", $gold);
        imagestring($im, 4, 710, $totY + 12, "750.0 M", $gold);

        // Catatan
        $noteY = $totY + 60;
        imagefilledrectangle($im, 40, $noteY, 810, $noteY + 80, $boxBg);
        imagerectangle($im, 40, $noteY, 810, $noteY + 80, $lineColor);
        imagestring($im, 3, 55, $noteY + 12, "CATATAN & INSTRUKSI PENGIRIMAN:", $headerBg);
        imagestring($im, 2, 55, $noteY + 34, wordwrap($incoming['catatan'], 95, "\n"), $textDark);
        imagestring($im, 2, 55, $noteY + 56, "Pengecekan rol kain telah sesuai dengan toleransi standar pabrik (50 m/rol utuh).", $grayText);

        // Bagian Tanda Tangan
        $sigY = $noteY + 110;
        imagestring($im, 3, 80, $sigY, "Pengirim (Gudang Supplier)", $textDark);
        imagestring($im, 3, 350, $sigY, "Sopir / Ekspedisi", $textDark);
        imagestring($im, 3, 600, $sigY, "Penerima (MitraSeratBuana)", $textDark);

        // Garis tanda tangan
        imageline($im, 60, $sigY + 80, 240, $sigY + 80, $grayText);
        imagestring($im, 3, 80, $sigY + 85, "(" . $supplier->nama_supplier . ")", $grayText);

        imageline($im, 320, $sigY + 80, 500, $sigY + 80, $grayText);
        imagestring($im, 3, 350, $sigY + 85, "(" . ($incoming['sopir'] ?? 'Bpk. Sopir') . ")", $grayText);

        imageline($im, 580, $sigY + 80, 760, $sigY + 80, $grayText);
        imagestring($im, 3, 600, $sigY + 85, "(Staff Gudang MitraSeratBuana)", $grayText);

        // Stempel Verifikasi "VERIFIED / DITERIMA GUDANG"
        imagerectangle($im, 570, $sigY + 15, 770, $sigY + 65, $stampGreen);
        imagerectangle($im, 572, $sigY + 17, 768, $sigY + 63, $stampGreen);
        imagestring($im, 4, 595, $sigY + 24, "DITERIMA GUDANG", $stampGreen);
        imagestring($im, 2, 605, $sigY + 44, date('d-m-Y H:i') . " WIB", $stampGreen);

        // Footer Barcode Line
        imagestring($im, 2, 40, $h - 40, "Dokumen Cetak Elektronik Sistem MitraSeratBuana | Dilampirkan otomatis pada penerimaan barang masuk", $grayText);
        imagestring($im, 2, 650, $h - 40, "Hal 1 dari 1 (Asli)", $grayText);

        imagepng($im, $path);
        imagedestroy($im);
    }
}
