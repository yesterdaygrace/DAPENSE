<?php

namespace Database\Factories;

use App\Models\Periode;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodeFactory extends Factory
{
    protected $model = Periode::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-2 years', 'now');
        $end = (clone $start)->modify('+11 months');

        return [
            'nama_periode' => $this->faker->year() . ' - ' . $this->faker->monthName(),
            'tanggal_awal' => $start->format('Y-m-d'),
            'tanggal_akhir' => $end->format('Y-m-d'),
            'is_rekap' => false,
        ];
    }
}
