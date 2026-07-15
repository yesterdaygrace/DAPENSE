<?php

namespace Database\Factories;

use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\Periode;
use Illuminate\Database\Eloquent\Factories\Factory;

class JurnalingFactory extends Factory
{
    protected $model = Jurnaling::class;

    public function definition(): array
    {
        return [
            'tanggal_jurnal' => $this->faker->date(),
            'nomor_bukti' => $this->faker->unique()->bothify('KM-####/##/##'),
            'keterangan' => $this->faker->sentence(),
            'kategori_jurnal' => $this->faker->randomElement(['Kas Masuk', 'Kas Keluar', 'Bank Masuk', 'Bank Keluar', 'Memorial']),
            'debit' => $this->faker->numberBetween(10000, 10000000),
            'kredit' => 0,
            'coa_id' => COA::factory(),
            'periode_id' => Periode::factory(),
        ];
    }
}
