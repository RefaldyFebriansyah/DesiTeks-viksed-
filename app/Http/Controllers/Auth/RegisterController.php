<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran akun mitra supplier.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun supplier baru.
     * Otomatis membuat entitas Supplier di database admin dan menghubungkannya dengan User supplier.
     */
    public function register(Request $request)
    {
        if ($request->has('no_telepon') && $request->input('no_telepon') !== null) {
            $rawPhone = trim((string) $request->input('no_telepon'));
            $digits = preg_replace('/[^0-9]/', '', $rawPhone);
            if (str_starts_with($digits, '62')) {
                $phone = '+' . $digits;
            } elseif (str_starts_with($digits, '0')) {
                $phone = '+62' . substr($digits, 1);
            } else {
                $phone = '+62' . $digits;
            }
            $request->merge(['no_telepon' => $phone]);
        }

        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:255'],
            'name'          => ['required', 'string', 'max:255'],
            'username'      => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email'         => ['required', 'string', 'email', 'max:150', 'unique:users,email', 'regex:/^[a-zA-Z0-9._%+\-]+@gmail\.com$/i'],
            'no_telepon'    => ['required', 'string', 'regex:/^\+62[0-9]{8,13}$/'],
            'asal_kota'     => ['required', 'string', 'max:100'],
            'alamat'        => ['nullable', 'string', 'max:500'],
            'password'      => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
        ], [
            'nama_supplier.required' => 'Nama perusahaan / pabrik kain wajib diisi.',
            'name.required'          => 'Nama penanggung jawab (PIC) wajib diisi.',
            'username.required'      => 'Username akun wajib diisi.',
            'username.unique'        => 'Username ini sudah digunakan, silakan pilih username lain.',
            'username.alpha_dash'    => 'Username hanya boleh berisi huruf, angka, tanda strip, dan garis bawah.',
            'email.required'         => 'Alamat email wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email ini sudah terdaftar di sistem.',
            'email.regex'            => 'Email wajib menggunakan domain @gmail.com (contoh: nama@gmail.com).',
            'no_telepon.required'    => 'Nomor telepon / WhatsApp wajib diisi.',
            'no_telepon.regex'       => 'Nomor telepon harus diawali dengan +62 dan hanya berisi angka (contoh: +6281234567890).',
            'asal_kota.required'     => 'Kota domisili supplier wajib diisi.',
            'password.required'      => 'Password akun wajib diisi.',
            'password.min'           => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed'     => 'Konfirmasi password tidak cocok.',
            'password.regex'         => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        $user = DB::transaction(function () use ($validated) {
            // 1. Generate kode_supplier otomatis (format: SUP001, SUP002, dst)
            $lastSupplier = Supplier::orderBy('id', 'desc')->first();
            $nextSeq = 1;
            if ($lastSupplier && $lastSupplier->kode_supplier) {
                $num = (int) preg_replace('/[^0-9]/', '', $lastSupplier->kode_supplier);
                $nextSeq = $num + 1;
            }
            $kodeSupplier = 'SUP' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

            // 2. Buat data Master Supplier yang langsung masuk ke panel Admin
            $supplier = Supplier::create([
                'kode_supplier' => $kodeSupplier,
                'nama_supplier' => $validated['nama_supplier'],
                'email'         => $validated['email'],
                'no_telepon'    => $validated['no_telepon'],
                'asal_kota'     => $validated['asal_kota'],
                'alamat'        => $validated['alamat'] ?? null,
            ]);

            // 3. Tentukan cabang default untuk user supplier
            $defaultBranch = Branch::where('is_main', true)->first() ?? Branch::first();

            // 4. Buat Akun User untuk login
            $user = User::create([
                'name'        => $validated['name'],
                'username'    => strtolower($validated['username']),
                'email'       => $validated['email'],
                'password'    => Hash::make($validated['password']),
                'role'        => 'supplier',
                'status'      => 'aktif',
                'branch_id'   => $defaultBranch?->id ?? 1,
                'supplier_id' => $supplier->id,
            ]);

            // 5. Catat ke Audit Log sistem
            AuditLog::create([
                'user_id'   => $user->id,
                'aktivitas' => "Pendaftaran mitra supplier baru: {$supplier->nama_supplier} (Kode: {$supplier->kode_supplier})",
                'model'     => 'Supplier',
                'model_id'  => $supplier->id,
            ]);

            return $user;
        });

        // Login otomatis setelah registrasi berhasil
        Auth::login($user);

        return redirect()->route('supplier.dashboard')
            ->with('success', "Selamat datang, {$user->name}! Akun mitra supplier Anda ({$user->supplier?->nama_supplier}) berhasil didaftarkan dan aktif di sistem MitraSeratBuana.");
    }
}
