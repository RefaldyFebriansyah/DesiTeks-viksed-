<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'items'               => 'required|array|min:1',
            'items.*.fabric_id'   => 'required|exists:fabrics,id',
            'items.*.satuan'      => 'required|in:meter,rol',
            'items.*.jumlah'      => 'required|numeric|min:0.01',
            'items.*.harga_satuan'=> 'required|numeric|min:0',
            'jumlah_bayar'        => 'required|numeric|min:0',
            'metode'              => 'required|in:tunai,transfer,qris',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'                => 'Keranjang belanja tidak boleh kosong.',
            'items.*.fabric_id.required'    => 'Pilih kain untuk setiap item.',
            'items.*.satuan.required'       => 'Satuan wajib dipilih.',
            'items.*.jumlah.required'       => 'Jumlah wajib diisi.',
            'items.*.jumlah.min'            => 'Jumlah minimal 0.01.',
            'items.*.harga_satuan.required' => 'Harga satuan wajib diisi.',
            'jumlah_bayar.required'         => 'Jumlah pembayaran wajib diisi.',
            'metode.required'               => 'Metode pembayaran wajib dipilih.',
        ];
    }
}
