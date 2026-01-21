<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageCompressionService;
use Illuminate\Support\Facades\Log;

class TestImageCompression extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:test-compression {--file= : Path to test image file}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Test image compression dengan berbagai quality settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️  Testing Image Compression Service...');
        $this->newLine();

        // Buat test image jika belum ada file yang diberikan
        $testImagePath = $this->option('file');
        
        if (!$testImagePath || !file_exists($testImagePath)) {
            $this->info('📝 Membuat test image...');
            $testImagePath = storage_path('app/test_image.jpg');
            
            // Create a simple test image
            $image = imagecreatetruecolor(800, 600);
            $bgColor = imagecolorallocate($image, 255, 255, 255);
            $textColor = imagecolorallocate($image, 0, 0, 0);
            imagefill($image, 0, 0, $bgColor);
            imagestring($image, 5, 10, 10, 'Test Image for Compression', $textColor);
            
            imagejpeg($image, $testImagePath, 90);
            imagedestroy($image);
            
            $this->info("✅ Test image dibuat di: {$testImagePath}");
        }

        // Read test image
        $originalData = file_get_contents($testImagePath);
        $this->info("📊 Original image size: " . round(strlen($originalData) / 1024, 2) . " KB");
        $this->newLine();

        // Initialize compression service
        $compressionService = new ImageCompressionService();

        // Test dengan berbagai quality levels
        $qualityLevels = [50, 60, 70, 75, 80, 90];
        
        $this->line('Testing WebP Compression dengan quality settings berbeda:');
        $this->newLine();

        $table_data = [];

        foreach ($qualityLevels as $quality) {
            try {
                $compressed = $compressionService->compressImage($originalData, quality: $quality);
                $comparison = $compressionService->compareSize($originalData, $compressed);

                $table_data[] = [
                    $quality . '%',
                    $comparison['compressed_kb'] . ' KB',
                    $comparison['reduction_percentage'] . '%',
                    ($comparison['original_kb'] - $comparison['compressed_kb']) . ' KB'
                ];

                $this->info(
                    "Quality {$quality}% → " .
                    "{$comparison['compressed_kb']} KB " .
                    "(Pengurangan: {$comparison['reduction_percentage']}%)"
                );
            } catch (\Exception $e) {
                $this->error("❌ Error pada quality {$quality}%: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->table(
            ['Quality', 'Size', 'Reduction', 'Saved Space'],
            $table_data
        );

        $this->newLine();
        $this->info('✅ Test selesai!');
        $this->info('💡 Rekomendasi: Gunakan quality 75% untuk keseimbangan ukuran dan kualitas');
    }
}
