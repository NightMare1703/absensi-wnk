<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Services\ImageCompressionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreAttendanceRequest;

/**
 * Contoh-contoh penggunaan ImageCompressionService
 * 
 * File ini adalah referensi untuk berbagai cara menggunakan compression service
 */
class ImageCompressionExamples
{
    /**
     * Example 1: Kompresi dengan setting default
     */
    public function example1_DefaultCompression($photoBase64)
    {
        $service = new ImageCompressionService();
        
        // Kompresi dengan setting default (quality 75, max 1280x720)
        $compressed = $service->compressImage($photoBase64);
        
        return $compressed;
    }

    /**
     * Example 2: Kompresi dengan quality custom
     */
    public function example2_CustomQuality($photoBase64)
    {
        $service = new ImageCompressionService();
        
        // High quality untuk archive
        $compressed = $service->compressImage($photoBase64, quality: 90);
        
        return $compressed;
    }

    /**
     * Example 3: Kompresi dengan resize untuk storage terbatas
     */
    public function example3_AggressiveCompression($photoBase64)
    {
        $service = new ImageCompressionService();
        
        // Ukuran lebih kecil untuk storage sangat terbatas
        $compressed = $service->compressImage(
            $photoBase64,
            quality: 60,
            maxWidth: 640,
            maxHeight: 480
        );
        
        return $compressed;
    }

    /**
     * Example 4: Kompresi dengan comparison info
     */
    public function example4_WithComparison($photoBase64)
    {
        $service = new ImageCompressionService();
        
        // Get original info
        $originalInfo = $service->getImageInfo($photoBase64);
        
        // Compress
        $compressed = $service->compressImage($photoBase64);
        
        // Get compressed info
        $compressedInfo = $service->getImageInfo($compressed);
        
        // Compare
        $comparison = $service->compareSize($photoBase64, $compressed);
        
        return [
            'original' => $originalInfo,
            'compressed' => $compressedInfo,
            'comparison' => $comparison
        ];
    }

    /**
     * Example 5: Kompresi multiple images
     */
    public function example5_MultipleImages($imageArray)
    {
        $service = new ImageCompressionService();
        
        $results = [];
        
        foreach ($imageArray as $index => $imageData) {
            $compressed = $service->compressImage($imageData);
            $comparison = $service->compareSize($imageData, $compressed);
            
            $results[] = [
                'index' => $index,
                'reduction_percentage' => $comparison['reduction_percentage'],
                'saved_kb' => $comparison['reduction_kb']
            ];
        }
        
        return $results;
    }

    /**
     * Example 6: Fallback ke JPEG jika WebP error
     */
    public function example6_WithFallback($photoBase64)
    {
        $service = new ImageCompressionService();
        
        try {
            // Try WebP first
            $compressed = $service->compressImage($photoBase64);
            $extension = 'webp';
        } catch (\Exception $e) {
            // Fallback ke JPEG
            $result = $service->compressImageJpeg($photoBase64);
            $compressed = $result['data'];
            $extension = $result['extension'];
            
            Log::warning('WebP compression failed, using JPEG instead: ' . $e->getMessage());
        }
        
        return [
            'data' => $compressed,
            'extension' => $extension
        ];
    }

    /**
     * Example 7: Batch processing dengan progress logging
     */
    public function example7_BatchProcessing($imageDirectory)
    {
        $service = new ImageCompressionService();
        $files = glob($imageDirectory . '/*.jpg');
        
        $totalSaved = 0;
        
        foreach ($files as $file) {
            $originalData = file_get_contents($file);
            $compressed = $service->compressImage($originalData);
            $comparison = $service->compareSize($originalData, $compressed);
            
            $totalSaved += $comparison['reduction_kb'];
            
            Log::info("Compressed: {$file} - Saved: {$comparison['reduction_kb']} KB");
        }
        
        return [
            'files_processed' => count($files),
            'total_saved_kb' => $totalSaved,
            'total_saved_mb' => round($totalSaved / 1024, 2)
        ];
    }

    /**
     * Example 8: Dynamic quality based on user preference
     */
    public function example8_DynamicQuality($photoBase64, $user)
    {
        $service = new ImageCompressionService();
        
        // Set quality berdasarkan user preference
        $quality = match($user->plan) {
            'premium' => 90,
            'standard' => 75,
            'free' => 60,
            default => 75
        };
        
        $compressed = $service->compressImage($photoBase64, quality: $quality);
        
        return $compressed;
    }

    /**
     * Example 9: Store compressed image dengan metadata
     */
    public function example9_StoreWithMetadata($photoBase64, $userId)
    {
        $service = new ImageCompressionService();
        
        $now = Carbon::now();
        $originalInfo = $service->getImageInfo($photoBase64);
        $compressed = $service->compressImage($photoBase64);
        $compressedInfo = $service->getImageInfo($compressed);
        $comparison = $service->compareSize($photoBase64, $compressed);
        
        // Save to storage
        $fileName = "absensi/user_{$userId}_{$now->timestamp}.webp";
        Storage::disk('public')->put($fileName, $compressed);
        
        // Return detailed info
        return [
            'file_path' => $fileName,
            'original_kb' => $originalInfo['size_kb'],
            'compressed_kb' => $compressedInfo['size_kb'],
            'reduction_percentage' => $comparison['reduction_percentage'],
            'timestamp' => $now->toDateTimeString()
        ];
    }

    /**
     * Example 10: Clean up old uncompressed images
     */
    public function example10_CleanupOldImages($daysOld = 30)
    {
        $service = new ImageCompressionService();
        
        $files = Storage::disk('public')->files('absensi');
        $now = now();
        $cleaned = [];
        
        foreach ($files as $file) {
            // Skip already compressed files (webp)
            if (str_ends_with($file, '.webp')) {
                continue;
            }
            
            $lastModified = Storage::disk('public')->lastModified($file);
            $lastModifiedDate = \Carbon\Carbon::createFromTimestamp($lastModified);
            
            // Delete if older than X days
            if ($now->diffInDays($lastModifiedDate) > $daysOld) {
                Storage::disk('public')->delete($file);
                $cleaned[] = $file;
            }
        }
        
        return [
            'files_cleaned' => count($cleaned),
            'files' => $cleaned
        ];
    }
}
