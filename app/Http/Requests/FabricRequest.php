<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FabricRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        if ($this->nama_kategori === '__custom__' && $this->filled('nama_kategori_custom')) {
            $this->merge([
                'nama_kategori' => trim($this->nama_kategori_custom)
            ]);
        }
    }

    public function rules(): array
    {
        $fabricId = $this->route('fabric')?->id;

        return [
            'kode_kain'       => 'required|string|max:50|unique:fabrics,kode_kain,' . $fabricId,
            'nama_kain'       => 'required|string|max:100',
            'nama_kategori'   => 'required|string|max:100',
            'jenis_kain'      => 'nullable|string|max:100',
            'warna'           => 'nullable|string|max:100',
            'motif'           => 'nullable|string|max:100',
            'harga_per_meter' => 'required|numeric|min:0',
            'harga_per_rol'   => 'required|numeric|min:0',
            'meter_per_rol'   => 'required|numeric|min:0.01',
            'stok_minimum'    => 'nullable|integer|min:0',
            'stok_maksimum'   => 'nullable|integer|min:0',
            'status'          => 'required|in:aktif,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kain.required'       => 'Kode kain wajib diisi.',
            'kode_kain.unique'         => 'Kode kain sudah digunakan.',
            'nama_kain.required'       => 'Nama kain wajib diisi.',
            'nama_kategori.required'   => 'Kategori kain wajib diisi.',
            'harga_per_meter.required' => 'Harga per meter wajib diisi.',
            'harga_per_rol.required'   => 'Harga per rol wajib diisi.',
        ];
    }
}
