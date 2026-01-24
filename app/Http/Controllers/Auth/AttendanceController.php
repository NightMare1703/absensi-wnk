<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Services\ImageCompressionService;
use App\Services\AttendanceStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Http\Controllers\Controller;
use App\Models\WorkLocation;
use App\Models\WorkShift;

class AttendanceController extends Controller
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
        $statusService = new AttendanceStatusService();
        $status = $statusService->determineStatus($shift_id);
        
        // ===== COMPRESS IMAGE SEBELUM DISIMPAN =====
        // Initialize compression info variable
        $compressionInfo = [
            'original_kb' => 0,
            'compressed_kb' => 0,
            'reduction_percentage' => 0,
        ];
        
        // Check apakah compression enabled
        if (!config('image-compression.enabled', true)) {
            // Jika disabled, simpan tanpa kompresi
            $fileName = 'absensi' . '/' . $name . '_' . $now->timestamp . '.' . $extension;
            Storage::disk('public')->put($fileName, $photoBase64);
            $photoPath = $fileName;
        } else {
            // Kompresi gambar
            $compressionService = new ImageCompressionService();
            
            // Info gambar sebelum kompresi
            $originalInfo = $compressionService->getImageInfo($photoBase64);
            
            // Kompresi gambar dengan setting dari config
            $quality = config('image-compression.quality', 75);
            $maxWidth = config('image-compression.max_width', 1280);
            $maxHeight = config('image-compression.max_height', 720);
            $format = config('image-compression.format', 'webp');
            
            $compressedImageData = $compressionService->compressImage(
                $photoBase64,
                quality: $quality,
                maxWidth: $maxWidth,
                maxHeight: $maxHeight
            );
            
            // Info gambar setelah kompresi
            $compressedInfo = $compressionService->getImageInfo($compressedImageData);
            
            // Bandingkan ukuran
            $sizeComparison = $compressionService->compareSize($photoBase64, $compressedImageData);
            
            // Update compression info
            $compressionInfo = [
                'original_kb' => round($sizeComparison['original_kb'] ?? 0, 2),
                'compressed_kb' => round($sizeComparison['compressed_kb'] ?? 0, 2),
                'reduction_percentage' => $sizeComparison['reduction_percentage'] ?? 0,
            ];
            
            // Log hasil kompresi untuk monitoring
            if (config('image-compression.logging', true)) {
                Log::info('Image Compression Result', [
                    'user_id' => $nameId,
                    'user_name' => $name,
                    'timestamp' => $now->toDateTimeString(),
                    'original_size_kb' => $originalInfo['size_kb'] ?? 0,
                    'compressed_size_kb' => $compressedInfo['size_kb'] ?? 0,
                    'reduction_percentage' => $sizeComparison['reduction_percentage'] ?? 0,
                    'quality' => $quality,
                ]);
            }
            
            // Store photo dengan format WebP
            $fileName = 'absensi' . '/' . $name . '_' . $now->timestamp . '.' . $format;
            Storage::disk('public')->put($fileName, $compressedImageData);
            $photoPath = $fileName;
        }

        // Create attendance record
        $attendance = Attendance::create([
            'user_id' => $nameId,
            'date' => $date,
            'shift_id' => $shift_id,
            'location_id' => $location_id,
            'check_in' => $time,
            'picture_check_in' => $photoPath,
            'status' => $status,
        ]);

        // Log attendance creation
        Log::info('Attendance created', [
            'user_id' => $nameId,
            'attendance_id' => $attendance->id,
            'date' => $date,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Absensi Berhasil Tercatat',
            'redirect' => '/dashboard',
            'compression_info' => $compressionInfo
        ], 201);
        
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
        // Authorization using policy
        try {
            $this->authorize('update', $attendance);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('Attendance edit - Unauthorized access attempt', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role,
                'attendance_id' => $attendance->id,
                'attendance_owner' => $attendance->user_id,
            ]);
            abort(403, 'Anda tidak berhak mengubah kehadiran pengguna lain.');
        }

        $locations = WorkLocation::orderBy('location', 'asc')->get();
        $shifts = WorkShift::orderBy('shift', 'asc')->get();

        return view('edit-attendance', compact('attendance', 'locations', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        // Authorization using policy - Strict check
        try {
            $this->authorize('update', $attendance);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('Attendance update - Unauthorized access attempt', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role,
                'attendance_id' => $attendance->id,
                'attendance_owner' => $attendance->user_id,
            ]);
            abort(403, 'Anda tidak berhak mengubah kehadiran pengguna lain.');
        }

        try {
            // Validate shift exists and get it
            $workShift = WorkShift::findOrFail($request->input('shift'));
            
            // Calculate attendance status based on original check-in time
            $statusService = new AttendanceStatusService();
            $checkInTime = $attendance->check_in ? 
                Carbon::createFromFormat('H:i:s', $attendance->check_in, 'Asia/Jakarta') : 
                Carbon::now('Asia/Jakarta');
            
            $status = $statusService->determineStatus($workShift->id, $checkInTime);

            // Update attendance record
            $attendance->update([
                'shift_id' => $request->input('shift'),
                'location_id' => $request->input('location'),
                'status' => $status,
            ]);

            Log::info('Attendance updated', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role,
                'attendance_id' => $attendance->id,
                'attendance_owner' => $attendance->user_id,
                'new_shift' => $workShift->id,
                'new_status' => $status,
            ]);
            
            return redirect()->route('dashboard')->with('success', 'Data absensi berhasil diperbarui.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Attendance update - Invalid shift', [
                'user_id' => Auth::user()->id,
                'shift_id' => $request->input('shift'),
            ]);
            return back()->withErrors(['shift' => 'Shift yang dipilih tidak valid.']);
            
        } catch (\Exception $e) {
            Log::error('Attendance update failed', [
                'user_id' => Auth::user()->id,
                'attendance_id' => $attendance->id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data absensi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

}
