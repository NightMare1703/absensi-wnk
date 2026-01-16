<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\JobReport;
use App\Models\Attendance;
use Livewire\WithPagination;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class AttendanceSearchAdmin extends Component
{
    use WithPagination;

    public $name = '';
    public $date_from = '';
    public $date_to = '';

    // Modal confirmation properties
    public $showDeleteConfirmation = false;
    public $deleteType = ''; // 'attendance' or 'report' or 'bulk'
    public $deleteAttendanceId;
    public $deleteReportId;
    public $deleteItemDate = '';
    public $deleteItemLabel = '';
    public $bulkDeleteCount = 0;

    public function updatingName()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new AttendanceExport($this->name, $this->date_from, $this->date_to),
            $this->name . '-' . Carbon::parse($this->date_from)->format('d-m-Y') . '-' . Carbon::parse($this->date_to)->format('d-m-Y') . '.xlsx'
        );
    }

    public function deleteReport($id)
    {
        $report = JobReport::find($id);
        if ($report) {
            $report->delete();
            session()->flash('message', 'Data laporan berhasil dihapus.');
        }
        $this->showDeleteConfirmation = false;
        $this->resetPage();
    }
    
    public function confirmDeleteReport($id)
    {
        $report = JobReport::find($id);
        if ($report) {
            $this->deleteReportId = $id;
            $this->deleteType = 'report';
            $this->deleteItemDate = Carbon::parse($report->work_date)->locale('id')->isoFormat('DD MMMM YYYY');
            $this->deleteItemLabel = $this->deleteItemDate;
            $this->showDeleteConfirmation = true;
        }
    }

    public function confirmDeleteAttendance($id)
    {
        $attendance = Attendance::find($id);
        if ($attendance) {
            $this->deleteAttendanceId = $id;
            $this->deleteType = 'attendance';
            $this->deleteItemDate = Carbon::parse($attendance->date)->locale('id')->isoFormat('DD MMMM YYYY');
            $this->deleteItemLabel = $this->deleteItemDate;
            $this->showDeleteConfirmation = true;
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteConfirmation = false;
        $this->deleteAttendanceId = null;
        $this->deleteReportId = null;
        $this->deleteType = '';
        $this->deleteItemDate = '';
        $this->bulkDeleteCount = 0;
    }

    public function proceedDelete()
    {
        if ($this->deleteType === 'report' && $this->deleteReportId) {
            $this->deleteReport($this->deleteReportId);
        } elseif ($this->deleteType === 'attendance' && $this->deleteAttendanceId) {
            $this->deleteAttendance($this->deleteAttendanceId);
        } elseif ($this->deleteType === 'bulk') {
            $this->bulkDeleteFilteredData();
        }
        $this->cancelDelete();
    }

    public function confirmBulkDelete()
    {
        $reportQuery = JobReport::query();
        $attendanceQuery = Attendance::query();

        if ($this->name) {
            $reportQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
            $attendanceQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
        }

        if ($this->date_from && $this->date_to) {
            $reportQuery->whereBetween('work_date', [$this->date_from, $this->date_to]);
            $attendanceQuery->whereBetween('date', [$this->date_from, $this->date_to]);
        }

        $reportCount = $reportQuery->count();
        $attendanceCount = $attendanceQuery->count();
        $totalCount = $reportCount + $attendanceCount;

        if ($totalCount > 0) {
            $this->deleteType = 'bulk';
            $this->bulkDeleteCount = $totalCount;
            $this->deleteItemLabel = "$reportCount Laporan Kerja + $attendanceCount Data Absensi";
            $this->showDeleteConfirmation = true;
        }
    }

    public function bulkDeleteFilteredData()
    {
        $reportQuery = JobReport::query();
        $attendanceQuery = Attendance::query();

        if ($this->name) {
            $reportQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
            $attendanceQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
        }

        if ($this->date_from && $this->date_to) {
            $reportQuery->whereBetween('work_date', [$this->date_from, $this->date_to]);
            $attendanceQuery->whereBetween('date', [$this->date_from, $this->date_to]);
        }

        $reports = $reportQuery->get();
        $attendances = $attendanceQuery->get();

        // Hapus semua laporan
        foreach ($reports as $report) {
            $report->delete();
        }

        // Hapus semua absensi beserta file gambar
        foreach ($attendances as $attendance) {
            if ($attendance->picture_check_in && Storage::disk('public')->exists($attendance->picture_check_in)) {
                Storage::disk('public')->delete($attendance->picture_check_in);
            }
            $attendance->delete();
        }

        session()->flash('message', "Berhasil menghapus {$reports->count()} laporan kerja dan {$attendances->count()} data absensi.");
        $this->showDeleteConfirmation = false;
    }

    public function deleteAttendance($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        // Hapus file gambar jika ada
        if ($attendance->picture_check_in && Storage::disk('public')->exists($attendance->picture_check_in)) {
            Storage::disk('public')->delete($attendance->picture_check_in);
        }

        // Hapus data absensi dari database
        $attendance->delete();

        session()->flash('message', 'Data absensi berhasil dihapus.');
        $this->showDeleteConfirmation = false;

    }

    public function render()
    {
        $reportQuery = JobReport::query();

        if ($this->name) {
            $reportQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
        }

        if ($this->date_from && $this->date_to) {
            $reportQuery->whereBetween('work_date', [$this->date_from, $this->date_to]);
        }

        // 
        $query = Attendance::query();

        if ($this->name) {
            $query->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
        }

        if ($this->date_from && $this->date_to) {
            $query->whereBetween('date', [$this->date_from, $this->date_to]);
        }

        if($this->date_from == '' && $this->date_to == '' || $this->name == ''){
            $reportQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            })->whereMonth('work_date', Carbon::now()->month)->whereYear('work_date', Carbon::now()->year);
            $query->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            })->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year);
        }
            $reports = $reportQuery->orderBy('work_date')->paginate(31);
            $attendances = $query->orderBy('date')->paginate(31);

        $users = User::orderBy('name', 'asc')->get();
        return view(
            'livewire.attendance-search-admin',
            [
                'reports' => $reports,
                'attendances' => $attendances,
                'users' => $users
            ]
        );
    }
}
