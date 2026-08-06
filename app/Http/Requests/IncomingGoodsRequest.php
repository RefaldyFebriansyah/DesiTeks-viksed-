<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncomingGoodsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tanggal'              => 'required|date',
            'supplier_id'          => 'nullable',
            'nama_supplier'        => 'required|string|max:150',
            'nomor_faktur'         => 'required|string|max:100|unique:incoming_goods,nomor_faktur',
            'catatan'              => 'nullable|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.fabric_id'    => 'required|string',
            // fields if new fabric
            'items.*.kode_kain'       => 'nullable|string|max:50',
            'items.*.nama_kain'       => 'required_if:items.*.fabric_id,new|nullable|string|max:100',
            'items.*.nama_kategori'   => 'required_if:items.*.fabric_id,new|nullable|string|max:100',
            'items.*.jenis_kain'      => 'nullable|string|max:100',
            'items.*.warna'           => 'nullable|string|max:100',
            'items.*.harga_per_meter' => 'nullable|numeric|min:0',
            'items.*.harga_per_rol'   => 'nullable|numeric|min:0',
            // quantities
            'items.*.jumlah_rol'      => 'required|integer|min:0',
            'items.*.jumlah_meter'    => 'required|numeric|min:0.01',
            'items.*.harga_beli'      => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required'             => 'Tanggal wajib diisi.',
            'nama_supplier.required'       => 'Nama Supplier / PT wajib diisi.',
            'nomor_faktur.required'        => 'Nomor faktur wajib diisi.',
            'nomor_faktur.unique'          => 'Nomor faktur sudah pernah digunakan.',
            'items.required'               => 'Minimal satu item kain harus ditambahkan.',
            'items.*.jumlah_rol.required'  => 'Jumlah rol wajib diisi.',
            'items.*.jumlah_meter.required' => 'Jumlah meter wajib diisi.',
            'items.*.harga_beli.required'  => 'Harga beli wajib diisi.',
        ];
    }
}
