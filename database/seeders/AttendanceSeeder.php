<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\WorkLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkLocation::insert([
            ['location'=>'M1'],
            ['location'=>'M2'],
            ['location'=>'M3'],
            ['location'=>'M4'],
            ['location'=>'C1'],
            ['location'=>'C2'],
            ['location'=>'C3'],
            ['location'=>'C4'],
            ['location'=>'C5'],
            ['location'=>'C6']
        ]);
    }
}
