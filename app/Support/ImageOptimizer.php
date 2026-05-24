<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    public static function store(UploadedFile $file, string $directory, int $maxWidth = 1600, int $maxHeight = 1600, int $quality = 82): string
    {
        $contents = file_get_contents($file->getRealPath());
        $source = $contents ? @imagecreatefromstring($contents) : false;

        if (!$source) {
            return $file->store($directory, 'public');
        }

        self::fixOrientation($source, $file);

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, $maxWidth / $width, $maxHeight / $height);

        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);

        $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $extension = function_exists('imagewebp') ? 'webp' : 'jpg';
        $path = trim($directory, '/') . '/' . Str::uuid() . '.' . $extension;

        ob_start();

        if ($extension === 'webp') {
            imagewebp($target, null, $quality);
        } else {
            $jpg = imagecreatetruecolor($targetWidth, $targetHeight);
            $white = imagecolorallocate($jpg, 255, 255, 255);
            imagefilledrectangle($jpg, 0, 0, $targetWidth, $targetHeight, $white);
            imagecopy($jpg, $target, 0, 0, 0, 0, $targetWidth, $targetHeight);
            imagejpeg($jpg, null, $quality);
            imagedestroy($jpg);
        }

        $optimized = ob_get_clean();

        imagedestroy($source);
        imagedestroy($target);

        if (!$optimized) {
            return $file->store($directory, 'public');
        }

        Storage::disk('public')->put($path, $optimized);

        return $path;
    }

    private static function fixOrientation(&$image, UploadedFile $file): void
    {
        if (!function_exists('exif_read_data')) {
            return;
        }

        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'], true)) {
            return;
        }

        $exif = @exif_read_data($file->getRealPath());
        $orientation = $exif['Orientation'] ?? null;

        if ($orientation === 3) {
            $image = imagerotate($image, 180, 0);
        }

        if ($orientation === 6) {
            $image = imagerotate($image, -90, 0);
        }

        if ($orientation === 8) {
            $image = imagerotate($image, 90, 0);
        }
    }
}
