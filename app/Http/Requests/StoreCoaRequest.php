<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $coaId = $this->route('id');

        return [
            'kode_akun' => 'required|string|max:255|unique:coas,kode_akun,' . $coaId,
            'nama_akun' => 'required|string|max:255',
            'tipe_akun' => 'required|string|max:50',
            'header_id' => 'nullable|exists:header_coas,id',
            'saldo_normal' => 'nullable|string|max:10',
            'mata_uang' => 'nullable|string|max:10',
        ];
    }
}
