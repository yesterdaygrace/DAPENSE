<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJurnalingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:50',
            'keterangan' => 'required|string|max:500',
            'coa_id' => 'required|array',
            'coa_id.*' => 'required|exists:coas,id',
            'debit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit' => 'required|array',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'nullable|string|max:50',
        ];
    }
}
