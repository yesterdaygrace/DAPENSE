<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHeaderCoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $headerId = $this->route('id');

        return [
            'kode_header' => 'required|string|max:255|unique:header_coas,kode_header,' . $headerId,
            'nama_header' => 'required|string|max:255',
            'level' => 'required|integer',
            'parent_id' => 'nullable|exists:header_coas,id',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_header.unique' => 'Kode header sudah ada, silakan gunakan kode lain.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nama_header')) {
            $this->merge([
                'nama_header' => strtoupper($this->nama_header),
            ]);
        }
    }
}
