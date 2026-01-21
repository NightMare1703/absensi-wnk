<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;

class ImageCompressionService
{
    protected $imageManager;

    public function __construct()
    {
        // Menggunakan GD driver (built-in PHP)
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Compress image dengan WebP format untuk ukuran lebih kecil
     * 
     * @param string $imageData Binary data dari gambar (base64 decoded)
     * @param int $quality Kualitas kompresi (1-100, default 75)
     * @param int $maxWidth Maksimal lebar gambar (px)
     * @param int $maxHeight Maksimal tinggi gambar (px)
     * @return string Binary data dari gambar yang sudah dikompres
     */
    public function compressImage(
        string $imageData,
        int $quality = 75,
        int $maxWidth = 1280,
        int $maxHeight = 720
    ): string {
        try {
            // Read image dari binary data
            $image = $this->imageManager->read($imageData);

            // Jika gambar lebih besar dari ukuran maksimal, resize
            if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                $image->scale(width: $maxWidth, height: $maxHeight);
            }

            // Encode ke WebP format dengan quality tertentu
            // WebP memberikan kompresi lebih baik dari JPEG/PNG
            $compressed = $image->toWebp(quality: $quality);

            return $compressed->toString();
        } catch (\Exception $e) {
            // Fallback ke original data jika ada error
            Log::error('Image compression failed: ' . $e->getMessage());
            return $imageData;
        }
    }

    /**
     * Compress image dengan JPEG format (alternatif jika WebP tidak support)
     * 
     * @param string $imageData Binary data dari gambar
     * @param int $quality Kualitas kompresi (1-100, default 75)
     * @param int $maxWidth Maksimal lebar gambar (px)
     * @param int $maxHeight Maksimal tinggi gambar (px)
     * @return array Array berisi ['data' => binary, 'extension' => 'jpg']
     */
    public function compressImageJpeg(
        string $imageData,
        int $quality = 75,
        int $maxWidth = 1280,
        int $maxHeight = 720
    ): array {
        try {
            $image = $this->imageManager->read($imageData);

            // Resize jika diperlukan
            if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                $image->scale(width: $maxWidth, height: $maxHeight);
            }

            // Encode ke JPEG
            $compressed = $image->toJpeg(quality: $quality);

            return [
                'data' => $compressed->toString(),
                'extension' => 'jpg'
            ];
        } catch (\Exception $e) {
            Log::error('JPEG compression failed: ' . $e->getMessage());
            return [
                'data' => $imageData,
                'extension' => 'png'
            ];
        }
    }

    /**
     * Get image info (ukuran original sebelum dan sesudah kompresi)
     * 
     * @param string $imageData Binary data
     * @return array Info tentang gambar
     */
    public function getImageInfo(string $imageData): array
    {
        try {
            $image = $this->imageManager->read($imageData);
            
            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'size_bytes' => strlen($imageData),
                'size_kb' => round(strlen($imageData) / 1024, 2)
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Compare ukuran sebelum dan sesudah kompresi
     * 
     * @param string $originalData Original binary data
     * @param string $compressedData Compressed binary data
     * @return array Perbandingan ukuran dan persentase pengurangan
     */
    public function compareSize(string $originalData, string $compressedData): array
    {
        $originalSize = strlen($originalData);
        $compressedSize = strlen($compressedData);
        $reduction = $originalSize - $compressedSize;
        $percentage = $originalSize > 0 ? round(($reduction / $originalSize) * 100, 2) : 0;

        return [
            'original_bytes' => $originalSize,
            'original_kb' => round($originalSize / 1024, 2),
            'compressed_bytes' => $compressedSize,
            'compressed_kb' => round($compressedSize / 1024, 2),
            'reduction_bytes' => $reduction,
            'reduction_kb' => round($reduction / 1024, 2),
            'reduction_percentage' => $percentage
        ];
    }
}
