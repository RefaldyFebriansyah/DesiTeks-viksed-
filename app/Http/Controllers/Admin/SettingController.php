<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $depan = Setting::getVal('nama_depan_toko', '');
        $belakang = Setting::getVal('nama_belakang_toko', '');
        $full = Setting::getVal('nama_toko', 'KainKita');

        if (empty($depan) && empty($belakang)) {
            $depan = $full;
            $belakang = '';
        }

        $settings = [
            'nama_depan_toko'    => $depan,
            'nama_belakang_toko' => $belakang,
            'nama_toko'          => $full,
            'alamat_toko'        => Setting::getVal('alamat_toko', ''),
            'telepon_toko'       => Setting::getVal('telepon_toko', ''),
            'catatan_struk'      => Setting::getVal('catatan_struk', ''),
            'pengumuman_supplier'=> Setting::getVal('pengumuman_supplier', 'Harap periksa kelengkapan kain, jumlah rol, dan surat jalan sebelum pengiriman ke gudang.'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_depan_toko'    => 'required|string|max:30',
            'nama_belakang_toko' => 'nullable|string|max:30',
            'alamat_toko'        => 'nullable|string|max:255',
            'telepon_toko'       => 'nullable|string|max:30',
            'catatan_struk'      => 'nullable|string|max:500',
            'pengumuman_supplier'=> 'nullable|string|max:1000',
        ]);

        $depan = trim($request->nama_depan_toko);
        $belakang = trim($request->nama_belakang_toko);

        // Jika nama belakang diisi, bersihkan spasi & pastikan huruf pertama KAPITAL
        if (!empty($belakang)) {
            $belakang = preg_replace('/\s+/', '', $belakang);
            $belakang = ucfirst($belakang);
        }

        $fullNamaToko = $depan . $belakang;

        Setting::setVal('nama_depan_toko', $depan);
        Setting::setVal('nama_belakang_toko', $belakang);
        Setting::setVal('nama_toko', $fullNamaToko);
        Setting::setVal('alamat_toko', $request->alamat_toko);
        Setting::setVal('telepon_toko', $request->telepon_toko);
        Setting::setVal('catatan_struk', $request->catatan_struk);
        Setting::setVal('pengumuman_supplier', $request->pengumuman_supplier);

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Memperbarui Pengaturan Aplikasi / Toko (' . $fullNamaToko . ')',
            'model'     => 'Setting',
            'model_id'  => 0, // General setting
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan nama toko (' . $fullNamaToko . ') berhasil disimpan.');
    }
}
