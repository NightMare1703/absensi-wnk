<?php

namespace Database\Seeders;

use App\Models\WorkShift;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkShift::insert(
            [
                [
                    'shift' => 'Pagi',
                    'late' => '07:00:00'
                ],
                [
                    'shift' => 'Siang',
                    'late' => '15:00:00'
                ],
                [
                    'shift' => 'Malam',
                    'late' => '23:00:00'
                ],
                [
                    'shift' => 'Antara',
                    'late' => '12:00:00'
                ],
                [
                    'shift' => 'SM2a',
                    'late' => '15:00:00'
                ],
                [
                    'shift' => 'SM2b',
                    'late' => '19:00:00'
                ],
            ]
        );
    }
}
