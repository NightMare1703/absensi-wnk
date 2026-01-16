<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\JobReport;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\{
    WithMultipleSheets,
    WithProperties
};

class AttendanceExport implements WithMultipleSheets, WithProperties
{
    protected $name;
    protected $date_from;
    protected $date_to;

    public function __construct($name = null, $date_from = null, $date_to = null)
    {
        $this->name  = $name;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
    }

    /**
     * RETURN MULTIPLE SHEETS
     */
    public function sheets(): array
    {
        return [
            new AttendanceSheet($this->name, $this->date_from, $this->date_to),
            new JobReportSheet($this->name, $this->date_from, $this->date_to),
        ];
    }

    /**
     * DOCUMENT PROPERTIES
     */
    public function properties(): array
    {
        return [
            'creator'        => 'WNK System',
            'title'          => 'Laporan Kehadiran & Kerja',
            'description'    => 'Laporan Kehadiran dan Laporan Kerja',
            'subject'        => 'Attendance & Job Report',
            'keywords'       => 'absensi,laporan kerja',
            'category'       => 'Reports',
            'manager'        => 'Admin',
            'company'        => 'WNK',
        ];
    }
}
