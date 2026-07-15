<?php

namespace Database\Factories;

use App\Models\COA;
use App\Models\HeaderCOA;
use Illuminate\Database\Eloquent\Factories\Factory;

class COAFactory extends Factory
{
    protected $model = COA::class;

    public function definition(): array
    {
        return [
            'kode_akun' => (string) $this->faker->unique()->numberBetween(1000, 99999),
            'nama_akun' => $this->faker->words(3, true),
            'saldo_normal' => $this->faker->randomElement(['Debit', 'Kredit']),
            'kategori' => $this->faker->randomElement(['Aktiva Lancar', 'Aktiva Tetap', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban']),
            'level' => (string) $this->faker->numberBetween(1, 5),
            'header_coa_id' => HeaderCOA::factory(),
        ];
    }
}
