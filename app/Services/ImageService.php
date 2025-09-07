<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

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
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $supported = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $supported, true)) {
            $ext = 'jpg';
        }
        $hashed = $file->hashName($dir);
        $path = preg_replace('/\.[^.]+$/', '.' . $ext, $hashed) ?: ($dir . '/' . Str::random() . '.' . $ext);
        $encoded = $img->encodeByExtension($ext, quality: $quality);
        Storage::disk('public')->put($path, (string) $encoded);
        
        return Storage::url($path);
    }
}