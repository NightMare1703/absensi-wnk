<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkLocation>
 */
class WorkLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location' => $this->faker->randomElements(['M1', 'M2', 'M3', 'M4', 'C1', 'C2', 'C3', 'C4', 'C5', 'C6']),
        ];
    }
}
