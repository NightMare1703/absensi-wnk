<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkLocation;
use App\Models\WorkShift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        return [
            'user_id' => User::factory(),
            'date' => $this->faker->dateTimeThisYear('now', 'asia/Jakarta'),
            'location_id' => $this->faker->numberBetween('1', '12'),
            'shift_id' => $this->faker->numberBetween('1', '6'),
            'check_in' => $this->faker->time('H:i', 'now'),
            'picture_check_in' => null,
            'status' => $this->faker->randomElement(['Terlambat', 'Tidak Terlambat']),
            'created_at' => $this->faker->dateTimeThisYear('now','asia/Jakarta'),
        ];
    }
}
