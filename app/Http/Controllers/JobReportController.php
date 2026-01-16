<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\JobReport;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreJobReportRequest;
use App\Http\Requests\UpdateJobReportRequest;

class JobReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('job-report');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobReportRequest $request)
    {
        JobReport::create([
            'user_id'       => Auth::user()->id,
            'work_date'     => $request->date,
            'hours'         => $request->hours,
            'rate'          => 10000,
            'total_salary'  => $request->hours * 10000,
            'description'   => $request->description,
        ]);

        return view('dashboard')->with('success', 'Laporan kerja berhasil dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JobReport $jobReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobReport $jobReport)
    {
        // Authorization: Check if the user owns this job report
        if ($jobReport->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized access to this job report.');
        }

        return view('edit-job-report', compact('jobReport'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobReportRequest $request, JobReport $jobReport)
    {
        // Authorization: Check if the user owns this job report
        if ($jobReport->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized access to this job report.');
        }

        // Get validated data from the form request
        $data = $request->validated();

        // Map form fields to DB column names and compute derived values
        $data['work_date'] = $request->input('date');
        $data['hours'] = $request->input('hours');
        $data['description'] = $request->input('description');
        $data['total_salary'] = $request->input('hours') * 10000;

        // Remove 'date' if present to avoid mass-assignment mismatch
        if (array_key_exists('date', $data)) {
            unset($data['date']);
        }

        $jobReport->update($data);

        return redirect()->route('dashboard')->with('success', 'Laporan kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobReport $jobReport)
    {
        //
    }
}
