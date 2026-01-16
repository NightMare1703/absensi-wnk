<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AttendanceController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\JobReportController;

Route::middleware(['auth', 'verified'])->group(function(){
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    // dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // send attendance data
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance', [AttendanceController::class, 'store']);

    // job report route
    Route::get('/report', [JobReportController::class, 'index'])->name('job-report');
    Route::post('/report', [JobReportController::class, 'store']);
    Route::get('/report/edit/{jobReport}', [JobReportController::class, 'edit'])->name('edit-job-report');
    Route::put('/report/{jobReport}', [JobReportController::class, 'update'])->name('update-job-report');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
