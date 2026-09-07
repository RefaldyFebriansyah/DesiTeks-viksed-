<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        if ($this->has('no_telepon') && !empty($this->no_telepon)) {
            $phone = trim($this->no_telepon);
            // Clean non-digit except +
            $phone = preg_replace('/[^\d+]/', '', $phone);

            if (!empty($phone)) {
                if (str_starts_with($phone, '0')) {
                    $phone = '+62' . substr($phone, 1);
                } elseif (str_starts_with($phone, '8')) {
                    $phone = '+62' . $phone;
                } elseif (str_starts_with($phone, '62') && !str_starts_with($phone, '+')) {
                    $phone = '+' . $phone;
                } elseif (!str_starts_with($phone, '+')) {
                    $phone = '+' . $phone;
                }
            }

            $this->merge([
                'no_telepon' => $phone,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nama_supplier' => 'required|string|max:150',
            'email'         => 'nullable|email|max:150',
            'asal_kota'     => 'nullable|string|max:100',
            'alamat'        => 'nullable|string|max:500',
            'no_telepon'    => 'nullable|string|max:30',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
        ];
    }
}
