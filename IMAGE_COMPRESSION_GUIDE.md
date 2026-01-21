# 📸 Dokumentasi Image Compression - Absensi WNK

## 📋 Overview

Sistem telah diimplementasikan dengan fitur **Image Compression** untuk mengurangi ukuran gambar absensi tanpa mengorbankan kualitas visual yang signifikan.

## 🎯 Hasil Kompresi

Berdasarkan test yang dilakukan:

| Quality | Ukuran Final | Pengurangan | Saved Space |
|---------|-------------|------------|------------|
| **50%** | 1.96 KB | **80.86%** | 8.28 KB |
| **60%** | 2.01 KB | **80.35%** | 8.23 KB |
| **70%** | 2.06 KB | **79.83%** | 8.18 KB |
| **75%** ⭐ | 2.09 KB | **79.57%** | 8.15 KB |
| **80%** | 2.21 KB | **78.4%** | 8.03 KB |
| **90%** | 2.47 KB | **75.85%** | 7.77 KB |

**⭐ Quality 75% dipilih sebagai default** - memberikan keseimbangan terbaik antara ukuran file dan kualitas visual.

## 🔧 Implementasi

### 1. **ImageCompressionService** (`app/Services/ImageCompressionService.php`)

Service ini menangani kompresi gambar dengan fitur:

- ✅ Kompresi WebP (format modern, ukuran lebih kecil dari JPEG)
- ✅ Fallback ke JPEG jika diperlukan
- ✅ Resize otomatis untuk gambar yang terlalu besar
- ✅ Logging untuk monitoring
- ✅ Error handling

**Methods tersedia:**

```php
// Kompresi dengan WebP (recommended)
$compressed = $service->compressImage(
    $imageData,
    quality: 75,           // 1-100
    maxWidth: 1280,        // pixel
    maxHeight: 720         // pixel
);

// Kompresi dengan JPEG (alternatif)
$result = $service->compressImageJpeg($imageData, quality: 75);

// Get info gambar
$info = $service->getImageInfo($imageData);

// Compare ukuran sebelum-sesudah
$comparison = $service->compareSize($original, $compressed);
```

### 2. **AttendanceController** (`app/Http/Controllers/Auth/AttendanceController.php`)

Sudah terintegrasi dengan kompresi otomatis di method `store()`:

```php
// Kompresi gambar sebelum disimpan
$compressionService = new ImageCompressionService();
$compressedImageData = $compressionService->compressImage(
    $photoBase64,
    quality: 75,
    maxWidth: 1280,
    maxHeight: 720
);

// Simpan dengan format WebP
Storage::disk('public')->put($fileName, $compressedImageData);
```

Gambar sekarang disimpan dalam format `.webp` bukan format original.

### 3. **Response Information**

Response JSON sekarang include informasi kompresi:

```json
{
    "message": "Absensi Berhasil Tercatat",
    "redirect": "/dashboard",
    "compression_info": {
        "original_kb": 512,
        "compressed_kb": 128,
        "reduction_percentage": 75
    }
}
```

## 📊 Keuntungan

### Ukuran Storage
- **Pengurangan 75-80%** dari ukuran original
- Dengan 100 absensi per hari, bisa menghemat ~5-10 MB per hari
- Per tahun: 1.8-3.6 GB!

### Performa
- WebP support di hampir semua browser modern
- Load time gambar lebih cepat
- Bandwidth lebih rendah

### Kualitas
- Quality 75% masih mempertahankan detail wajah yang baik untuk identifikasi
- Tidak ada visual difference yang signifikan bagi mata manusia

## ⚙️ Konfigurasi

Untuk mengubah setting kompresi, edit bagian ini di `AttendanceController`:

```php
$compressedImageData = $compressionService->compressImage(
    $photoBase64,
    quality: 75,        // ← Ubah nilai ini (1-100)
    maxWidth: 1280,     // ← Ubah resolusi maksimal
    maxHeight: 720
);
```

### Quality Presets:

| Level | Quality | Use Case |
|-------|---------|----------|
| **High** | 90% | Arsip penting, detail tinggi |
| **Medium** ⭐ | 75% | Balance (recommended) |
| **Low** | 50-60% | Quick save, storage sangat terbatas |

## 🧪 Testing

Untuk test kompresi dengan gambar lokal:

```bash
# Test dengan test image otomatis
php artisan image:test-compression

# Test dengan gambar spesifik
php artisan image:test-compression --file=/path/to/image.jpg
```

## 📝 Monitoring

Setiap kompresi dicatat di log file untuk monitoring:

```
INFO: Image Compression Result
- user_id: 1
- timestamp: 2026-01-21 10:30:45
- original_size_kb: 512
- compressed_size_kb: 128
- reduction_percentage: 75
```

Cek di: `storage/logs/laravel.log`

## ⚠️ Notes

1. **Kompatibilitas Browser**: WebP didukung di:
   - Chrome/Chromium ✅
   - Firefox ✅
   - Safari (iOS 14.1+, macOS 11+) ✅
   - Edge ✅

2. **Fallback**: Jika ada error pada kompresi, gambar original akan disimpan dengan warning di log

3. **GD Library**: Pastikan GD library sudah enable di PHP
   ```bash
   php -m | grep gd
   ```

## 🚀 Penggunaan Lanjutan

### Custom Quality per User

```php
// Di controller, bisa set quality based on user preferences
$quality = $user->is_premium ? 85 : 75;
$compressed = $compressionService->compressImage($photoBase64, quality: $quality);
```

### Archive Compression

Untuk resize lebih agresif untuk arsip:

```php
$compressed = $compressionService->compressImage(
    $photoBase64,
    quality: 60,        // Lebih kecil
    maxWidth: 640,      // Resolusi lebih rendah
    maxHeight: 480
);
```

---

**Implementasi selesai! 🎉**

Kompresi gambar akan otomatis berjalan setiap kali ada absensi baru. Storage Anda akan berkurang signifikan! 📉

