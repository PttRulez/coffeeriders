<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Throwable;

class ImageService
{
    public function save(
        UploadedFile $file,
        string $dir = 'uploads',
        int $maxW = 1920,
        int $maxH = 1920,
        int $quality = 80
    ): string {
        $img = Image::read($file)->scaleDown($maxW, $maxH);
        $path = $dir . '/' . Str::random(40) . '.webp';

        try {
            $encoded = $img->encodeByExtension('webp', quality: $quality);
        } catch (Throwable) {
            // Fallback for environments without WebP codec support.
            $path = $dir . '/' . Str::random(40) . '.jpg';
            $encoded = $img->encodeByExtension('jpg', quality: $quality);
        }

        Storage::disk('public')->put($path, (string) $encoded);
        
        return Storage::url($path);
    }

    public function delete(string $url): bool
    {
        $path = str_replace('/storage/', '', $url);

        return Storage::disk('public')->delete($path);
    }
}
