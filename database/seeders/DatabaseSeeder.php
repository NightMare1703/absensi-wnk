<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JobReport;
use App\Models\WorkShift;
use App\Models\Attendance;
use Illuminate\Support\Str;
use App\Models\WorkLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()
            ->count(10)
            ->has(Attendance::factory()->count(300))
            ->has(JobReport::factory()->count(300))
            ->create();

    }
}
