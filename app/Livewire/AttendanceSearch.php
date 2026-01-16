<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;

use App\Models\User;
use Livewire\Component;
use App\Models\Attendance;
use Livewire\WithPagination;
use App\Exports\AttendanceExport;
use App\Models\JobReport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceSearch extends Component
{
    use WithPagination;

    public $date_from = '';
    public $date_to = '';
    public $deleteAttendanceId;
    public $deleteReportId;

    public $thisMonth;

    // Modal confirmation properties
    public $showDeleteConfirmation = false;
    public $deleteType = ''; // 'attendance' or 'report'
    public $deleteItemDate = '';
    public $deleteItemLabel = '';

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
        $name = Auth::user()->name;
        return Excel::download(
            new AttendanceExport($name, $this->date_from),
            $name . '-' . Carbon::parse($this->date_from)->format('d-m-Y') . '-' . Carbon::parse($this->date_to)->format('d-m-Y') . '.xlsx'
        );
    }

    public function deleteReport($id)
    {
        $report = JobReport::find($id);
        if ($report) {
            $report->delete();
            session()->flash('message', 'Data laporan berhasi dihapus.');
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
        $attendance = Attendance::findOrFail($id);
        $this->deleteAttendanceId = $id;
        $this->deleteType = 'attendance';
        $this->deleteItemDate = Carbon::parse($attendance->date)->locale('id')->isoFormat('DD MMMM YYYY');
        $this->deleteItemLabel = $this->deleteItemDate;
        $this->showDeleteConfirmation = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteConfirmation = false;
        $this->deleteAttendanceId = null;
        $this->deleteReportId = null;
        $this->deleteType = '';
        $this->deleteItemDate = '';
    }

    public function proceedDelete()
    {
        if ($this->deleteType === 'report' && $this->deleteReportId) {
            $this->deleteReport($this->deleteReportId);
        } elseif ($this->deleteType === 'attendance' && $this->deleteAttendanceId) {
            $this->deleteAttendance($this->deleteAttendanceId);
        }
        $this->cancelDelete();
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
        $query = Attendance::query();
        
        if($this->date_from == '' && $this->date_to == ''){
            $reportQuery->where('user_id', Auth::id())->whereMonth('work_date', Carbon::now()->month)->whereYear('work_date', Carbon::now()->year);
            $query->where('user_id', Auth::id())->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year);
        }

        if ($this->date_from && $this->date_to) {
            $reportQuery->where('user_id', Auth::id())->whereBetween('work_date', [$this->date_from, $this->date_to]);
            $query->where('user_id', Auth::id())->whereBetween('date', [$this->date_from, $this->date_to]);
        }

        $reports = $reportQuery->where('user_id', Auth::id())->orderBy('work_date')->paginate(31);
        $attendances = $query->where('user_id', Auth::id())->orderBy('date')->paginate(31);
        
        return view(
            'livewire.attendance-search',
            [
                'reports' => $reports,
                'attendances' => $attendances,
            ]
        );
    }
}
