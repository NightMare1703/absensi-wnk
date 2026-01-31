<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\JobReport;
use App\Models\Attendance;
use Livewire\WithPagination;
use App\Exports\AttendanceExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class AttendanceSearchAdmin extends Component
{
    use WithPagination;

    public $name = '';
    public $date_from = '';
    public $date_to = '';

    // Edit role properties
    public $showEditRoleModal = false;
    public $editUserId = null;
    public $editUserRole = '';

    // Restrict roles to only two values as requested
    protected $rules = [
        'editUserRole' => 'required|in:admin,employee',
    ];

    protected $validationAttributes = [
        'editUserRole' => 'role pengguna',
    ];

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
            $this->name . '-' . ($this->date_from ? Carbon::parse($this->date_from)->format('d-m-Y') : 'all') . '-' . ($this->date_to ? Carbon::parse($this->date_to)->format('d-m-Y') : 'all') . '.xlsx'
        );
    }

    /**
     * Export only the users summary sheet (matching the UI table)
     */
    public function exportUsersSummary()
    {
        $from = $this->date_from ? Carbon::parse($this->date_from)->format('d-m-Y') : 'all';
        $to = $this->date_to ? Carbon::parse($this->date_to)->format('d-m-Y') : 'all';

        return Excel::download(
            new \App\Exports\UsersSummarySheet($this->name, $this->date_from, $this->date_to),
            'ringkasan-pengguna-' . $from . '-' . $to . '.xlsx'
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

    /**
     * Open edit-role modal for a user (admin only)
     */
    public function confirmEditRole($userId)
    {
        // allow admins to edit anyone, allow users to open modal for themselves only
        abort_if(!(Auth::user()->role === 'admin' || Auth::id() === (int) $userId), 403);

        $user = User::find($userId);
        if (! $user) {
            session()->flash('message', 'Pengguna tidak ditemukan.');
            return;
        }

        $this->editUserId = $user->id;
        $this->editUserRole = $user->role;
        $this->showEditRoleModal = true;
    }

    public function cancelEditRole()
    {
        $this->showEditRoleModal = false;
        $this->editUserId = null;
        $this->editUserRole = '';
        $this->resetValidation();
    }

    public function updateRole()
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        if (! $this->editUserId) {
            session()->flash('message', 'Tidak ada pengguna yang dipilih.');
            return;
        }

        $this->validate();

        $user = User::find($this->editUserId);
        if (! $user) {
            session()->flash('message', 'Pengguna tidak ditemukan.');
            $this->cancelEditRole();
            return;
        }

        $oldRole = $user->role;
        $user->role = $this->editUserRole;
        $user->save();

        // If the current user changed their own role, force re-login to refresh permissions
        if ($user->id === Auth::id()) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('login')->with('message', 'Peran Anda telah diubah menjadi "' . $user->role . '" — silakan login ulang.');
        }

        session()->flash('message', "Peran pengguna {$user->name} berhasil diubah dari {$oldRole} menjadi {$user->role}.");
        $this->cancelEditRole();
        $this->resetPage();
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
        // Query untuk pagination (data yang ditampilkan di tabel)
        $reportQuery = JobReport::query();
        $query = Attendance::query();

        if ($this->name) {
            $reportQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
            $query->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
        }

        if ($this->date_from && $this->date_to) {
            $reportQuery->whereBetween('work_date', [$this->date_from, $this->date_to]);
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

        // Query terpisah untuk menghitung statistik dari SEMUA data yang terfilter (bukan hanya halaman saat ini)
        $statsReportQuery = JobReport::query();
        $statsLateQuery = Attendance::query();

        if ($this->name) {
            $statsReportQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
            $statsLateQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            });
        }

        if ($this->date_from && $this->date_to) {
            $statsReportQuery->whereBetween('work_date', [$this->date_from, $this->date_to]);
            $statsLateQuery->whereBetween('date', [$this->date_from, $this->date_to]);
        }

        if($this->date_from == '' && $this->date_to == '' || $this->name == ''){
            $statsReportQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            })->whereMonth('work_date', Carbon::now()->month)->whereYear('work_date', Carbon::now()->year);
            $statsLateQuery->whereHas('user', function ($q) {
                $q->where('name', $this->name);
            })->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year);
        }
        
        // Calculate statistics dari SEMUA data terfilter
        $totalSalary = $statsReportQuery->sum('total_salary');
        $totalHours = number_format($statsReportQuery->sum('hours'),0, ',', '.');
        $lateCount = $statsLateQuery->where('status', 'Terlambat')->count();

        // Prepare local copies to safely use inside query closures
        $dateFrom = $this->date_from;
        $dateTo = $this->date_to;

        // Full user list for the dropdown (should NOT be limited by the current name filter)
        $allUsers = User::orderBy('name', 'asc')->get();

        // Per-user aggregates that respect the selected date range (or default to current month)
        // keep this as `$users` because the view expects it for the summary table
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

        return view(
            'livewire.attendance-search-admin',
            [
                'reports' => $reports,
                'attendances' => $attendances,
                'users' => $users,
                'allUsers' => $allUsers,
                'totalSalary' => $totalSalary,
                'totalHours' => $totalHours,
                'lateCount' => $lateCount
            ]
        );
    }
}
