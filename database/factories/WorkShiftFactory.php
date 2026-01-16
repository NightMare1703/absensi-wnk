<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkShift>
 */
class WorkShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'shift' => ['Pagi', 'Antara', 'Siang', 'SM2a', 'SM2b', 'Malam', 'Backup'],
            // 'late' => ['07:00:00', '12:00:00', '15:00:00', '15:00:00', '19:00:00', '23:00:00']
        ];
    }
}
