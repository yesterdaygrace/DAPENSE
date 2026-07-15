<?php

namespace Database\Factories;

use App\Models\Otorisator;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtorisatorFactory extends Factory
{
    protected $model = Otorisator::class;

    public function definition(): array
    {
        return [
            'nama_otorisator' => $this->faker->name(),
            'jabatan_otorisator' => $this->faker->jobTitle(),
        ];
    }
}
