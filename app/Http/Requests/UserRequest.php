<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $userId,
            'password' => $userId ? 'nullable|string|min:6' : 'required|string|min:6',
            'role'     => 'required|in:admin,gudang,kasir',
            'status'   => 'required|in:aktif,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'role.required'     => 'Role wajib dipilih.',
        ];
    }
}
