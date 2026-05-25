<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function image(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ], [
            'image.required' => 'No image was selected. Please choose a file to insert into the editor.',
            'image.uploaded' => 'The image could not be uploaded. This usually means the file exceeded the server upload limit; please choose a PNG, JPG, GIF, or WebP file under 2 MB.',
            'image.image' => 'The selected file is not a valid image. Please upload PNG, JPG, GIF, or WebP.',
            'image.mimes' => 'Unsupported image format. Only JPG, PNG, GIF, or WebP files are accepted.',
            'image.max' => 'The image is too large. Editor images can be at most 2 MB.',
        ]);

        $path = Storage::disk('public')->putFile('uploads', $validated['image']);

        if (! is_string($path) || $path === '') {
            return response()->json([
                'message' => 'The image file could not be saved. Please check file permissions and the storage link.',
            ], 500);
        }

        return response()->json([
            'url' => '/storage/'.ltrim($path, '/'),
        ]);
    }
}
