<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LifePost;
use App\Models\LifePostImage;
use App\Models\SiteSetting;
use App\Support\CroppedImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LifePostController extends Controller
{
    public function index()
    {
        return view('admin.life.index', [
            'admin' => true,
            'noindex' => true,
            'title' => 'My Life Yönetimi',
            'site' => SiteSetting::current()->site,
            'lifePosts' => LifePost::query()
                ->with('images')
                ->orderByDesc('published_at')
                ->latest()
                ->get(),
        ]);
    }

    public function create()
    {
        return view('admin.life.form', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Yeni Life Paylaşımı',
            'site' => SiteSetting::current()->site,
            'lifePost' => null,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $this->ensureImagesPresent($request);

        $lifePost = LifePost::query()->create($data);
        $this->syncUploadedImages($request, $lifePost);

        return redirect()->route('admin.life.edit', $lifePost)->with('status', 'Life paylaşımı oluşturuldu.');
    }

    public function edit(LifePost $lifePost)
    {
        $lifePost->load('images');

        return view('admin.life.form', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Life Paylaşımı Düzenle',
            'site' => SiteSetting::current()->site,
            'lifePost' => $lifePost,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, LifePost $lifePost)
    {
        $data = $this->validatedData($request);

        $lifePost->update($data);
        $this->syncUploadedImages($request, $lifePost);

        return redirect()->route('admin.life.edit', $lifePost)->with('status', 'Life paylaşımı kaydedildi.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'excerpt' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'visibility' => ['required', 'in:public,hidden,private'],
            'published_at' => ['nullable', 'date'],
            'keep_image_ids' => ['nullable', 'array'],
            'keep_image_ids.*' => ['integer'],
            'new_images' => ['nullable', 'array', 'max:20'],
            'new_images.*' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
            'new_image_crops' => ['nullable', 'json'],
        ], [
            'new_images.array' => 'Fotoğraflar doğru formatta gönderilemedi. Lütfen dosyaları yeniden seç.',
            'new_images.max' => 'Tek paylaşımda en fazla 20 fotoğraf yükleyebilirsin.',
            'new_images.*.uploaded' => 'Fotoğraflardan biri sunucuya yüklenemedi. Bu genelde fotoğraf boyutu 4 MB sınırını veya sunucunun upload limitini aştığında olur; lütfen daha küçük bir JPG, PNG, GIF veya WebP dosya seç.',
            'new_images.*.image' => 'Seçilen dosyalardan biri geçerli bir fotoğraf değil.',
            'new_images.*.mimes' => 'Fotoğraflar sadece JPG, PNG, GIF veya WebP formatında olabilir.',
            'new_images.*.max' => 'Fotoğraflardan biri çok büyük. Her fotoğraf en fazla 4 MB olabilir.',
            'new_image_crops.json' => 'Fotoğraf kırpma bilgileri doğru gönderilemedi. Lütfen fotoğrafları yeniden seçip kadrajla.',
            'keep_image_ids.array' => 'Mevcut fotoğraf seçimleri doğru gönderilemedi. Sayfayı yenileyip tekrar dene.',
            'keep_image_ids.*.integer' => 'Mevcut fotoğraf seçimlerinden biri geçersiz. Sayfayı yenileyip tekrar dene.',
            'published_at.date' => 'Tarih alanı geçerli bir tarih olmalı.',
            'visibility.in' => 'Görünürlük değeri geçersiz. Public, hidden veya private seçmelisin.',
        ]);

        return [
            'excerpt' => $validated['excerpt'],
            'location' => $validated['location'] ?? null,
            'visibility' => $validated['visibility'],
            'published_at' => $validated['published_at'] ?? null,
        ];
    }

    private function syncUploadedImages(Request $request, LifePost $lifePost): void
    {
        $keepIds = collect($request->input('keep_image_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $existingImages = $lifePost->images()->get();
        $newFiles = $request->file('new_images', []);
        $cropData = json_decode($request->input('new_image_crops', '[]'), true);

        if ($keepIds->isEmpty() && count($newFiles) === 0) {
            throw ValidationException::withMessages([
                'new_images' => 'En az bir fotoğraf yüklemelisin.',
            ]);
        }

        $existingImages
            ->reject(fn (LifePostImage $image): bool => $keepIds->contains($image->id))
            ->each(function (LifePostImage $image): void {
                $this->deleteStoredImage($image->url);
                $image->delete();
            });

        $sortOrder = 0;
        $lifePost->images()
            ->whereIn('id', $keepIds)
            ->orderBy('sort_order')
            ->get()
            ->each(function (LifePostImage $image) use (&$sortOrder): void {
                $image->update(['sort_order' => $sortOrder]);
                $sortOrder++;
            });

        foreach ($newFiles as $index => $file) {
            $crop = is_array($cropData) && isset($cropData[$index])
                ? json_encode($cropData[$index])
                : null;

            $lifePost->images()->create([
                'url' => app(CroppedImageStorage::class)->store($file, 'life', $crop),
                'alt' => $lifePost->excerpt,
                'sort_order' => $sortOrder,
            ]);

            $sortOrder++;
        }
    }

    private function ensureImagesPresent(Request $request): void
    {
        if (! $request->hasFile('new_images')) {
            throw ValidationException::withMessages([
                'new_images' => 'Yeni paylaşım oluştururken en az bir fotoğraf yüklemelisin.',
            ]);
        }
    }

    private function deleteStoredImage(string $url): void
    {
        if (! str_starts_with($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($url, strlen('/storage/')));
    }
}
