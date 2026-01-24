<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\WorkShift;

class AttendanceStatusService
{
    /**
     * Calculate attendance status based on shift timing
     */
    public function determineStatus(int $shiftId, ?Carbon $checkInTime = null): string
    {
        $shift = WorkShift::find($shiftId);
        
        if (!$shift || $shift->late === '' || $shift->late === null) {
            return 'Tidak Terlambat';
        }

        $checkTime = $checkInTime ?? Carbon::now('Asia/Jakarta');
        $shiftStartTime = Carbon::createFromFormat('H:i:s', $shift->late, 'Asia/Jakarta');
        
        // Set the date same as check time for proper comparison
        $shiftStartTime->setDate(
            $checkTime->year,
            $checkTime->month,
            $checkTime->day
        );

        return $checkTime->gt($shiftStartTime) ? 'Terlambat' : 'Tidak Terlambat';
    }
}
