<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('search')) {
            $query->where('name','like','%'.$request->search.'%')
                  ->orWhere('username','like','%'.$request->search.'%');
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        $users = $query->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'status'   => $request->status,
        ]);

        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menambah pengguna: {$user->name} ({$user->role})",
            'model'     => 'User',
            'model_id'  => $user->id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        // Admin hanya dapat melihat detail dalam mode Read-Only
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        return redirect()->route('admin.users.index')
            ->with('error', 'Akses ditolak: Admin hanya memiliki hak akses baca (Read-Only). Data pengguna tidak dapat diubah oleh Admin demi privasi.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        $nama = $user->name;
        $user->delete();
        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menghapus pengguna: {$nama}",
        ]);
        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$nama} berhasil dihapus.");
    }
}
