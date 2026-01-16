<?php

namespace App\Exports;

use App\Models\Attendance;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithDrawings,
    WithTitle
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class AttendanceSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithDrawings, WithTitle
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
     * GET ATTENDANCE DATA
     */
    public function collection()
    {
        $query = Attendance::query();

        if ($this->name) {
            $query->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
        }

        if ($this->date_from && $this->date_to) {
            $query->whereBetween('date', [$this->date_from, $this->date_to]);
        }

        return $query->orderBy('date')->get();
    }

    /**
     * HEADER EXCEL
     */
    public function headings(): array
    {
        return [
            'TANGGAL',
            'SHIFT',
            'LOKASI',
            'KEHADIRAN',
            'KETERANGAN',
            'FOTO ABSENSI',
        ];
    }

    /**
     * FORMAT ISI PER BARIS
     */
    public function map($row): array
    {
        return [
            Carbon::parse($row->date)->translatedFormat('d-m-Y'),
            $row->shift->shift,
            $row->location->location,
            $row->check_in,
            $row->status,
            '',
        ];
    }

    /**
     * ADD IMAGE TO EXCEL
     */
    public function drawings()
    {
        $drawings = [];
        $attendances = $this->collection();

        foreach ($attendances as $index => $attendance) {
            if (!$attendance->picture_check_in) {
                continue;
            }
            $drawing = new Drawing();
            $drawing->setName('Foto Absensi');
            $drawing->setDescription('Foto Absensi WNK');
            $drawing->setPath(storage_path('app/public/' . $attendance->picture_check_in));
            $drawing->setHeight(50);
            $drawing->setCoordinates('F' . ($index + 2)); // Column F, starting from row 2
            $drawings[] = $drawing;
        }

        return $drawings;
    }

    /**
     * STYLE EXCEL
     */
    public function styles(Worksheet $sheet)
    {
        // tinggi baris sesuaikan gambar
        foreach (range(2, $sheet->getHighestRow()) as $row) {
            $sheet->getRowDimension($row)->setRowHeight(15);
        }

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
        return 'Kehadiran';
    }
}
