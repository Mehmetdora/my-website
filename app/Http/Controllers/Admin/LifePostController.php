<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LifePost;
use App\Models\LifePostImage;
use App\Models\SiteSetting;
use App\Support\CroppedImageStorage;
use App\Support\StoredAssetCleaner;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LifePostController extends Controller
{
    public function index()
    {
        return view('admin.life.index', [
            'admin' => true,
            'noindex' => true,
            'title' => 'My Life Management',
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
            'title' => 'New Life Post',
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

        return redirect()->route('admin.life.edit', $lifePost)->with('status', 'Life post created.');
    }

    public function edit(LifePost $lifePost)
    {
        $lifePost->load('images');

        return view('admin.life.form', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Edit Life Post',
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

        return redirect()->route('admin.life.edit', $lifePost)->with('status', 'Life post saved.');
    }

    public function destroy(LifePost $lifePost)
    {
        $lifePost->load('images');

        $lifePost->images->each(function (LifePostImage $image): void {
            $this->deleteStoredImage($image->url);
            $image->delete();
        });

        $lifePost->delete();

        return redirect()->route('admin.life.index')->with('status', 'Life post deleted.');
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
            'new_images.array' => 'Photos could not be submitted in the correct format. Please select the files again.',
            'new_images.max' => 'You can upload at most 20 photos in a single post.',
            'new_images.*.uploaded' => 'One of the photos could not be uploaded. This usually means it exceeded the 4 MB limit or the server upload limit; please choose a smaller JPG, PNG, GIF, or WebP file.',
            'new_images.*.image' => 'One of the selected files is not a valid photo.',
            'new_images.*.mimes' => 'Photos must be JPG, PNG, GIF, or WebP.',
            'new_images.*.max' => 'One of the photos is too large. Each photo can be at most 4 MB.',
            'new_image_crops.json' => 'Photo crop data could not be submitted correctly. Please select and crop the photos again.',
            'keep_image_ids.array' => 'Current photo selections could not be submitted correctly. Please refresh the page and try again.',
            'keep_image_ids.*.integer' => 'One of the current photo selections is invalid. Please refresh the page and try again.',
            'published_at.date' => 'The date field must be a valid date.',
            'visibility.in' => 'The visibility value is invalid. Please choose public, hidden, or private.',
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
                'new_images' => 'You must upload at least one photo.',
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
                'new_images' => 'You must upload at least one photo when creating a new post.',
            ]);
        }
    }

    private function deleteStoredImage(string $url): void
    {
        app(StoredAssetCleaner::class)->deleteFromUrl($url);
    }
}
