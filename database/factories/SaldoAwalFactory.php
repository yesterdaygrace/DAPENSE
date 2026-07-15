<?php

namespace Database\Factories;

use App\Models\COA;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaldoAwalFactory extends Factory
{
    protected $model = SaldoAwal::class;

    public function definition(): array
    {
        return [
            'coa_id' => COA::factory(),
            'tanggal_saldo' => $this->faker->date(),
            'debit' => $this->faker->numberBetween(0, 10000000),
            'kredit' => $this->faker->numberBetween(0, 10000000),
            'periode_id' => Periode::factory(),
        ];
    }
}
