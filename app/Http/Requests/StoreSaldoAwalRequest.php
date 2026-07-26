<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaldoAwalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'coa_id' => 'required|exists:coas,id',
            'tanggal_saldo' => 'required|date',
            'periode_id' => 'required|exists:periodes,id',
            'debit' => 'required|numeric',
        ];
    }
}
