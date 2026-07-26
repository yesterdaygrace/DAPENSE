<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOtorisatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nama_otorisator' => 'required|string|max:255',
            'jabatan_otorisator' => 'required|string|max:255',
        ];
    }
}
