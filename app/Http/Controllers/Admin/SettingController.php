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
        $settings = [
            'nama_toko'     => Setting::getVal('nama_toko', 'DesiTeks'),
            'alamat_toko'   => Setting::getVal('alamat_toko', ''),
            'telepon_toko'  => Setting::getVal('telepon_toko', ''),
            'catatan_struk' => Setting::getVal('catatan_struk', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_toko'    => 'required|string|max:50',
            'alamat_toko'  => 'nullable|string|max:255',
            'telepon_toko' => 'nullable|string|max:30',
            'catatan_struk'=> 'nullable|string|max:500',
        ]);

        Setting::setVal('nama_toko', $request->nama_toko);
        Setting::setVal('alamat_toko', $request->alamat_toko);
        Setting::setVal('telepon_toko', $request->telepon_toko);
        Setting::setVal('catatan_struk', $request->catatan_struk);

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Memperbarui Pengaturan Aplikasi / Toko',
            'model'     => 'Setting',
            'model_id'  => 0, // General setting
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan aplikasi berhasil disimpan.');
    }
}
