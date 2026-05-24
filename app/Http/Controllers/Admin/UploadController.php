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
            'image.required' => 'Görsel seçilmedi. Lütfen editöre eklemek için bir dosya seç.',
            'image.uploaded' => 'Görsel sunucuya yüklenemedi. Bu genelde dosya boyutu sunucunun upload limitini aştığında olur; editör içi görseller için 2 MB altında PNG, JPG, GIF veya WebP dosya seç.',
            'image.image' => 'Seçilen dosya geçerli bir görsel değil. PNG, JPG, GIF veya WebP yüklemelisin.',
            'image.mimes' => 'Desteklenmeyen görsel formatı. Sadece JPG, PNG, GIF veya WebP kabul edilir.',
            'image.max' => 'Görsel çok büyük. Editör içi görseller en fazla 2 MB olabilir.',
        ]);

        $path = Storage::disk('public')->putFile('uploads', $validated['image']);

        if (! is_string($path) || $path === '') {
            return response()->json([
                'message' => 'Görsel dosyası kaydedilemedi. Lütfen dosya izinlerini ve storage bağlantısını kontrol et.',
            ], 500);
        }

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
