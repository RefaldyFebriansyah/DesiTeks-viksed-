<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    private function getSupplier(): Supplier
    {
        $user = Auth::user();
        if ($user->supplier) {
            return $user->supplier;
        }

        $supplier = Supplier::firstOrCreate(
            ['nama_supplier' => $user->name],
            [
                'kode_supplier' => 'SUP' . str_pad(Supplier::count() + 1, 3, '0', STR_PAD_LEFT),
                'email'         => $user->email,
            ]
        );

        $user->update(['supplier_id' => $supplier->id]);
        return $supplier;
    }

    public function index()
    {
        $user = Auth::user();
        $supplier = $this->getSupplier();

        return view('supplier.profile.index', compact('user', 'supplier'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $supplier = $this->getSupplier();

        $request->validate([
            'pic_name'      => 'required|string|max:100',
            'nama_supplier' => 'required|string|max:150',
            'email'         => ['required', 'string', 'email', 'max:150', 'regex:/^[a-zA-Z0-9._%+\-]+@gmail\.com$/i'],
            'no_telepon'    => ['nullable', 'string', 'max:30', 'regex:/^[1-9][0-9]{7,14}$/'],
            'asal_kota'     => 'nullable|string|max:100',
            'alamat'        => 'nullable|string|max:500',
        ], [
            'pic_name.required'      => 'Nama Penanggung Jawab (PIC) wajib diisi.',
            'nama_supplier.required' => 'Nama Perusahaan / Supplier wajib diisi.',
            'email.required'         => 'Email resmi supplier wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
            'email.regex'            => 'Email harus menggunakan @gmail.com (contoh: nama@gmail.com).',
            'no_telepon.regex'       => 'Nomor telepon tidak boleh diawali angka 0. Masukkan langsung angka setelah +62.',
        ]);

        $supplier->update([
            'nama_supplier' => trim($request->nama_supplier),
            'email'         => trim($request->email),
            'no_telepon'    => trim($request->no_telepon),
            'asal_kota'     => trim($request->asal_kota),
            'alamat'        => trim($request->alamat),
        ]);

        $user->update([
            'name'  => trim($request->pic_name),
            'email' => trim($request->email),
        ]);

        AuditLog::create([
            'user_id'   => $user->id,
            'aktivitas' => "Supplier memperbarui profil perusahaan: {$supplier->nama_supplier}",
            'model'     => 'Supplier',
            'model_id'  => $supplier->id,
        ]);

        return back()->with('success', 'Profil Supplier & data kontak berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
        ], [
            'current_password.required'         => 'Password lama wajib diisi.',
            'current_password.current_password' => 'Password saat ini salah.',
            'password.required'                 => 'Password baru wajib diisi.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
            'password.min'                      => 'Password baru minimal harus 8 karakter.',
            'password.regex'                    => 'Password baru harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::create([
            'user_id'   => $user->id,
            'aktivitas' => "Supplier mengubah password akun",
            'model'     => 'User',
            'model_id'  => $user->id,
        ]);

        return back()->with('success', 'Password akun berhasil diubah. Silakan gunakan password baru pada sesi berikutnya.');
    }
}
