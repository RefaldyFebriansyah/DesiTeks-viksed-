<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama_supplier' => 'required|string|max:150',
            'alamat'        => 'nullable|string|max:500',
            'no_telepon'    => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
        ];
    }
}
