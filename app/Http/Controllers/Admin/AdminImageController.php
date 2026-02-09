<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminImageController extends Controller
{
    public function destroy(Request $request, ImageService $imageService)
    {
        $request->validate([
            'url' => 'required|string',
        ]);

        $deleted = $imageService->delete($request->input('url'));

        return response()->json(['success' => $deleted]);
    }

    public function upload(Request $request, ImageService $imageService)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp',
            'dir' => 'sometimes|string|max:50',
        ], [
            'image.required' => 'Необходимо выбрать изображение',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, jpg, png, gif, webp',
        ]);

        try {
            $file = $request->file('image');
            $dir = $request->input('dir', 'uploads');

            $url = $imageService->save($file, $dir);
            
            return response()->json([
                'success' => true,
                'url' => $url,
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке изображения',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
