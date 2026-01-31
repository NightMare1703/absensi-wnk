<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Attendance;
use App\Models\JobReport;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersSummarySheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $name;
    protected $date_from;
    protected $date_to;

    public function __construct($name = null, $date_from = null, $date_to = null)
    {
        $this->name = $name;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
    }

    /**
     * Return collection of arrays (rows) for the sheet
     */
    public function collection()
    {
        $dateFrom = $this->date_from;
        $dateTo = $this->date_to;

        // Build user query with aggregates to avoid N+1
        $users = User::query()
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->select('users.*')
            ->withCount(['attendances as attendance_count' => function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('date', [$dateFrom, $dateTo]);
                } else {
                    $q->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year);
                }
            }])
            ->withSum(['jobReports as total_work_hours' => function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('work_date', [$dateFrom, $dateTo]);
                } else {
                    $q->whereMonth('work_date', Carbon::now()->month)->whereYear('work_date', Carbon::now()->year);
                }
            }], 'hours')
            ->withCount(['attendances as total_late_minutes' => function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('date', [$dateFrom, $dateTo]);
                } else {
                    $q->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year);
                }
                $q->where('status', 'Terlambat');
            }])
            ->withSum(['jobReports as salary' => function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('work_date', [$dateFrom, $dateTo]);
                } else {
                    $q->whereMonth('work_date', Carbon::now()->month)->whereYear('work_date', Carbon::now()->year);
                }
            }], 'total_salary')
            ->orderBy('name', 'asc')
            ->get();

        $rows = collect();
        foreach ($users as $i => $user) {
            $rows->push([
                'no' => $i + 1,
                'name' => $user->name,
                'attendance_count' => $user->attendance_count ?? 0,
                'total_work_hours' => $user->total_work_hours ?? 0,
                'total_late_minutes' => $user->total_late_minutes ?? 0,
                'salary' => $user->salary ?? 0,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA',
            'KEHADIRAN',
            'JAM KERJA',
            'KETERLAMBATAN',
            'GAJI',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Pengguna';
    }
}
