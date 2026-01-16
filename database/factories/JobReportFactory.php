<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobReport>
 */
class JobReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'work_date'     => $this->faker->dateTimeThisYear(),
            'hours'         => $this->faker->randomNumber(2, false),
            'rate'          => 10000,
            'total_salary'  => function (array $attributes) {
                                return $attributes['hours'] * 10000;
            },
        ];
    }
}
