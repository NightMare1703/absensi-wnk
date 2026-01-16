<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Http\Controllers\Controller;
use App\Models\WorkLocation;
use App\Models\WorkShift;

class AttendanceController extends Auth
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $location = WorkLocation::orderBy('location', 'asc')->get();
        $shift = WorkShift::orderBy('shift', 'asc')->get();
        return view('attendance.index', ['locations' => $location, 'shifts' =>$shift]);
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
    public function store(StoreAttendanceRequest $request)
    {
        $request->validate([
            'picture_check_in' => 'required|string' // base64 encoded image
        ]);

        $name = Auth::user()->name;
        $nameId = Auth::user()->id;
        $shift_id = $request->input('shift');
        $location_id = $request->input('location');
        $photoBase64 = $request->input('picture_check_in');

        // Clean base64 string (remove data:image/...;base64,)
        if (preg_match('/^data:image\/(\w+);base64,/', $photoBase64, $type)) {
            $photoBase64 = substr($photoBase64, strpos($photoBase64, ',') + 1);
            $extension = $type[1];
        } else {
            // assume png if not provided
            $extension = 'png';
        }

        $photoBase64 = base64_decode($photoBase64);
        if ($photoBase64 === false) {
            return response()->json(['message' => 'Invalid photo'], 422);
        }

        // Get current time in Asia/Jakarta timezone
        $now = Carbon::now('Asia/Jakarta');
        $date = $now->toDateString();
        $time = $now->toTimeString();

        // Cek Keterlambatan
        $workShift = WorkShift::find($shift_id);
        if($workShift->late == ''){
            $status = 'Tidak Terlambat';
        }else{
            $shiftStartTime = Carbon::createFromFormat('H:i:s', $workShift->late, 'Asia/Jakarta');
            $status = $now->gt($shiftStartTime) ? 'Terlambat' : 'Tidak Terlambat';
        }
        
        // Store photo
        $fileName = 'absensi' . '/' . $name . '_' . $now->timestamp . '.' . $extension;
        Storage::disk('public')->put($fileName, $photoBase64);

        $photoPath =  $fileName;

        // Create attendance record
        Attendance::create([
            'user_id' => $nameId,
            'date' => $date,
            'shift_id' => $shift_id,
            'location_id' => $location_id,
            'check_in' => $time,
            'picture_check_in' => $photoPath,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Absensi Berhasil Tercatat',
            'redirect' => '/dashboard'
        ], 201,);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

}
