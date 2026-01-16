<?php

namespace App\Models;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkLocation extends Model
{
    /** @use HasFactory<\Database\Factories\WorkLocationFactory> */
    use HasFactory;

    protected $guarded = [];

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}
