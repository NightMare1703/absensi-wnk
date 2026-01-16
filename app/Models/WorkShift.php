<?php

namespace App\Models;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkShift extends Model
{
    /** @use HasFactory<\Database\Factories\WorkShiftFactory> */
    use HasFactory;

    protected $guarded = [];

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    // public function attendance()
    // {
    //     return $this->hasMany(Attendance::class);
    // }
}
