<?php

namespace App\Imports;

use App\Models\COA;
use App\Models\HeaderCOA;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class COAImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $skipErrors = [];

    public function rules(): array
    {
        return [
            'kode_akun' => 'required|string|max:255',
            'nama_akun' => 'required|string|max:255',
            'kategori' => 'required|string|in:ASSET,LIABILITY,EQUITY,REVENUE,EXPENSE',
            'saldo_normal' => 'required|string|in:Debit,Kredit',
            'level' => 'required|integer|min:1',
            'header' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_akun.required' => 'Kode akun wajib diisi.',
            'kode_akun.unique' => 'Kode akun sudah ada.',
            'nama_akun.required' => 'Nama akun wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
            'kategori.in' => 'Kategori harus salah satu dari: ASSET, LIABILITY, EQUITY, REVENUE, EXPENSE.',
            'saldo_normal.required' => 'Saldo normal wajib diisi.',
            'saldo_normal.in' => 'Saldo normal harus Debit atau Kredit.',
            'level.required' => 'Level wajib diisi.',
        ];
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $kodeAkun = trim($row['kode_akun'] ?? '');
            $namaAkun = trim($row['nama_akun'] ?? '');

            if (empty($kodeAkun) || empty($namaAkun)) {
                continue;
            }

            $headerCoaId = $this->resolveHeaderId($row['header'] ?? null);

            COA::updateOrCreate(
                ['kode_akun' => $kodeAkun],
                [
                    'nama_akun' => strtoupper($namaAkun),
                    'kategori' => strtoupper($row['kategori'] ?? ''),
                    'saldo_normal' => $row['saldo_normal'] ?? 'Debit',
                    'level' => $row['level'] ?? 1,
                    'header_coa_id' => $headerCoaId,
                ]
            );
        }
    }

    protected function resolveHeaderId(?string $headerRef): ?int
    {
        if (empty($headerRef)) {
            return null;
        }

        $headerRef = trim($headerRef);

        $header = HeaderCOA::where('kode_header', $headerRef)->first();

        if (!$header) {
            $header = HeaderCOA::where('nama_header', $headerRef)->first();
        }

        return $header?->id;
    }

    public function getErrors(): array
    {
        return $this->skipErrors;
    }
}
