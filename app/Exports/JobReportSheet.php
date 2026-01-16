<?php

namespace App\Exports;

use App\Models\JobReport;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JobReportSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
     * GET JOB REPORT DATA
     */
    public function collection()
    {
        $query = JobReport::query();

        if ($this->name) {
            $query->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
        }

        if ($this->date_from && $this->date_to) {
            $query->whereBetween('work_date', [$this->date_from, $this->date_to]);
        }

        return $query->orderBy('work_date')->get();
    }

    /**
     * HEADER EXCEL
     */
    public function headings(): array
    {
        return [
            'TANGGAL',
            'JAM KERJA',
            'TOTAL GAJI',
            'DESKRIPSI PEKERJAAN',
        ];
    }

    /**
     * FORMAT ISI PER BARIS
     */
    public function map($row): array
    {
        return [
            Carbon::parse($row->work_date)->translatedFormat('d-m-Y'),
            $row->hours,
            $row->total_salary,
            $row->description,
        ];
    }

    /**
     * STYLE EXCEL
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }

    /**
     * SHEET TITLE
     */
    public function title(): string
    {
        return 'Laporan Kerja';
    }
}
