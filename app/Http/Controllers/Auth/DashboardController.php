<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance::where('user_id', Auth::id())->paginate(31);
        $morningLate = Carbon::parse('07:00:00');
        $afternoonLate = Carbon::parse('15:00:00');
        $nightLate = Carbon::parse('23:00:00');
        return view('dashboard', ['attendances' => $attendances, 'morningLate' => $morningLate, 'afternoonLate' => $afternoonLate, 'nightLate' => $nightLate]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attendances = Auth::get();

        dd($attendances);
        // return view('dashboard', ['attendances' => $attendances]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
