<?php

namespace Database\Factories;

use App\Models\HeaderCOA;
use Illuminate\Database\Eloquent\Factories\Factory;

class HeaderCoaFactory extends Factory
{
    protected $model = HeaderCOA::class;

    public function definition(): array
    {
        return [
            'kode_header' => 'H' . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'nama_header' => $this->faker->words(3, true),
            'level' => (string) $this->faker->numberBetween(1, 3),
            'parent_id' => null,
        ];
    }
}
