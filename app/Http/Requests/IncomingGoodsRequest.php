<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncomingGoodsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if (empty($this->nomor_faktur)) {
            $merge['nomor_faktur'] = \App\Models\IncomingGood::generateNomorFaktur($this->nama_supplier);
        }

        // Kalkulasi total otomatis dari items jika ada
        if ($this->has('items') && is_array($this->items)) {
            $calcRol = 0;
            $calcMeter = 0;
            $calcBeli = 0;
            foreach ($this->items as $item) {
                if (!empty($item['fabric_id'])) {
                    $rol = (int) ($item['jumlah_rol'] ?? 0);
                    $meter = (float) ($item['jumlah_meter'] ?? 0);
                    $harga = (float) ($item['harga_beli'] ?? 0);
                    $calcRol += $rol;
                    $calcMeter += $meter;
                    $calcBeli += ($meter * $harga);
                }
            }
            if ($calcRol > 0 || $calcMeter > 0 || $calcBeli > 0) {
                $merge['total_rol'] = $calcRol;
                $merge['total_meter'] = $calcMeter;
                $merge['total_pembelian'] = $calcBeli;
            }
        }

        if ($this->filled('total_pembelian') && is_string($this->total_pembelian)) {
            $merge['total_pembelian'] = (float) str_replace(['Rp', '.', ' '], '', $this->total_pembelian);
        }
        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'tanggal'              => 'required|date',
            'supplier_id'          => 'nullable',
            'nama_supplier'        => 'required|string|max:150',
            'nomor_faktur'         => 'required|string|max:100|unique:incoming_goods,nomor_faktur',
            'total_rol'            => 'required|integer|min:0',
            'total_meter'          => 'required|numeric|min:0.01',
            'total_pembelian'      => 'required|numeric|min:0',
            'catatan'              => 'nullable|string|max:500',
            'foto_lampiran'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'items'                => 'required|array|min:1',
            'items.*.fabric_id'    => 'required|string',
            'items.*.jumlah_rol'   => 'required|integer|min:0',
            'items.*.jumlah_meter' => 'required|numeric|min:0.01',
            'items.*.harga_beli'   => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required'              => 'Tanggal wajib diisi.',
            'nama_supplier.required'        => 'Nama Supplier / PT wajib diisi.',
            'nomor_faktur.required'         => 'Nomor faktur wajib diisi.',
            'nomor_faktur.unique'           => 'Nomor faktur sudah pernah digunakan.',
            'total_rol.required'            => 'Total rol wajib diisi.',
            'total_meter.required'          => 'Total meter wajib diisi.',
            'total_pembelian.required'      => 'Total nominal pembelian wajib diisi.',
        ];
    }
}
