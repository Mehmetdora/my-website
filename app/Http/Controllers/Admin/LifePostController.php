<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LifePost;
use App\Models\LifePostImage;
use App\Models\SiteSetting;
use App\Support\CroppedImageStorage;
use App\Support\StoredAssetCleaner;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
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
        $this->ensureRequiredMediaPresent($request, null, $data['post_type']);

        $lifePost = LifePost::query()->create($data);
        $this->syncMedia($request, $lifePost);

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
        $this->ensureRequiredMediaPresent($request, $lifePost, $data['post_type']);

        $lifePost->update($data);
        $this->syncMedia($request, $lifePost);

        return redirect()->route('admin.life.edit', $lifePost)->with('status', 'Life post saved.');
    }

    public function destroy(LifePost $lifePost)
    {
        $lifePost->load('images');

        $lifePost->images->each(function (LifePostImage $image): void {
            $this->deleteStoredAsset($image->url);
            $image->delete();
        });
        $this->deleteStoredAsset($lifePost->audio_url);

        $lifePost->delete();

        return redirect()->route('admin.life.index')->with('status', 'Life post deleted.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'post_type' => ['required', 'in:image,audio'],
            'excerpt' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'visibility' => ['required', 'in:public,hidden,private'],
            'published_at' => ['nullable', 'date'],
            'keep_image_ids' => ['nullable', 'array'],
            'keep_image_ids.*' => ['integer'],
            'new_images' => ['nullable', 'array', 'max:20'],
            'new_images.*' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
            'new_image_crops' => ['nullable', 'json'],
            'new_audio' => ['nullable', 'file', 'max:20480'],
            'delete_audio' => ['nullable', 'boolean'],
        ], [
            'post_type.required' => 'Please choose whether this is a photo post or a record post.',
            'post_type.in' => 'The post type is invalid. Please choose photo post or record post.',
            'new_images.array' => 'Photos could not be submitted in the correct format. Please select the files again.',
            'new_images.max' => 'You can upload at most 20 photos in a single post.',
            'new_images.*.uploaded' => 'One of the photos could not be uploaded. This usually means it exceeded the 4 MB limit or the server upload limit; please choose a smaller JPG, PNG, GIF, or WebP file.',
            'new_images.*.image' => 'One of the selected files is not a valid photo.',
            'new_images.*.mimes' => 'Photos must be JPG, PNG, GIF, or WebP.',
            'new_images.*.max' => 'One of the photos is too large. Each photo can be at most 4 MB.',
            'new_image_crops.json' => 'Photo crop data could not be submitted correctly. Please select and crop the photos again.',
            'new_audio.uploaded' => 'The audio file could not be uploaded. This usually means it exceeded the 20 MB limit or the server upload limit; please choose a smaller audio file.',
            'new_audio.file' => 'The selected audio upload is not a valid file.',
            'new_audio.max' => 'The audio file is too large. Audio files can be at most 20 MB.',
            'keep_image_ids.array' => 'Current photo selections could not be submitted correctly. Please refresh the page and try again.',
            'keep_image_ids.*.integer' => 'One of the current photo selections is invalid. Please refresh the page and try again.',
            'published_at.date' => 'The date field must be a valid date.',
            'visibility.in' => 'The visibility value is invalid. Please choose public, hidden, or private.',
        ]);

        if ($validated['post_type'] === 'audio' && $request->hasFile('new_images')) {
            throw ValidationException::withMessages([
                'new_images' => 'Audio posts cannot contain photos. Choose Photo post if you want to upload images.',
            ]);
        }

        if ($validated['post_type'] === 'image' && $request->hasFile('new_audio')) {
            throw ValidationException::withMessages([
                'new_audio' => 'Photo posts cannot contain an audio file. Choose Record post if you want to upload a record.',
            ]);
        }

        if ($request->hasFile('new_audio')) {
            $this->validateAudioUpload($request->file('new_audio'));
        }

        return [
            'post_type' => $validated['post_type'],
            'excerpt' => $validated['excerpt'],
            'location' => $validated['location'] ?? null,
            'visibility' => $validated['visibility'],
            'published_at' => $validated['published_at'] ?? null,
        ];
    }

    private function syncMedia(Request $request, LifePost $lifePost): void
    {
        if ($lifePost->post_type === 'audio') {
            $this->deleteAllImages($lifePost);
            $this->syncUploadedAudio($request, $lifePost);

            return;
        }

        $this->deleteStoredAsset($lifePost->audio_url);
        $lifePost->forceFill([
            'audio_url' => null,
            'audio_name' => null,
            'audio_mime' => null,
            'audio_size' => null,
        ])->save();

        $this->syncUploadedImages($request, $lifePost);
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
                $this->deleteStoredAsset($image->url);
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

    private function syncUploadedAudio(Request $request, LifePost $lifePost): void
    {
        if ($request->boolean('delete_audio') || $request->hasFile('new_audio')) {
            $this->deleteStoredAsset($lifePost->audio_url);
            $lifePost->forceFill([
                'audio_url' => null,
                'audio_name' => null,
                'audio_mime' => null,
                'audio_size' => null,
            ])->save();
        }

        if ($request->hasFile('new_audio')) {
            $file = $request->file('new_audio');
            $path = $file->storeAs('life/audio', Str::uuid().'.'.$this->audioExtensionFor($file), 'public');

            $lifePost->forceFill([
                'audio_url' => '/storage/'.ltrim($path, '/'),
                'audio_name' => $file->getClientOriginalName(),
                'audio_mime' => $file->getMimeType(),
                'audio_size' => $file->getSize(),
            ])->save();
        }

        if (! $lifePost->fresh()->audio_url) {
            throw ValidationException::withMessages([
                'new_audio' => 'You must upload one audio file for a record post.',
            ]);
        }
    }

    private function ensureRequiredMediaPresent(Request $request, ?LifePost $lifePost, string $postType): void
    {
        if ($postType === 'audio') {
            $hasExistingAudio = $lifePost?->audio_url && ! $request->boolean('delete_audio');

            if (! $request->hasFile('new_audio') && ! $hasExistingAudio) {
                throw ValidationException::withMessages([
                    'new_audio' => 'You must upload one audio file for a record post.',
                ]);
            }

            return;
        }

        $keepIds = collect($request->input('keep_image_ids', []))->filter();
        $hasExistingImages = $lifePost
            ? $lifePost->images()->whereIn('id', $keepIds)->exists()
            : false;

        if (! $request->hasFile('new_images') && ! $hasExistingImages) {
            throw ValidationException::withMessages([
                'new_images' => $lifePost
                    ? 'You must keep at least one current photo or upload a new photo for a photo post.'
                    : 'You must upload at least one photo when creating a new photo post.',
            ]);
        }
    }

    private function deleteAllImages(LifePost $lifePost): void
    {
        $lifePost->images()->get()->each(function (LifePostImage $image): void {
            $this->deleteStoredAsset($image->url);
            $image->delete();
        });
    }

    private function validateAudioUpload(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = strtolower((string) $file->getMimeType());

        $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'];
        $allowedMimeTypes = [
            'audio/mpeg',
            'audio/mp3',
            'audio/wav',
            'audio/x-wav',
            'audio/ogg',
            'audio/mp4',
            'audio/x-m4a',
            'audio/aac',
            'audio/webm',
            'video/webm',
        ];

        $isM4aReportedAsMp4 = $extension === 'm4a' && $mimeType === 'video/mp4';

        if (! in_array($extension, $allowedExtensions, true) || (! in_array($mimeType, $allowedMimeTypes, true) && ! $isM4aReportedAsMp4)) {
            throw ValidationException::withMessages([
                'new_audio' => 'Audio must be MP3, WAV, OGG, M4A, AAC, or WebM. If this is an M4A file, please make sure the file extension is .m4a.',
            ]);
        }
    }

    private function audioExtensionFor(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'], true)) {
            return $extension;
        }

        return match ($file->getMimeType()) {
            'audio/wav', 'audio/x-wav' => 'wav',
            'audio/ogg' => 'ogg',
            'audio/mp4', 'audio/x-m4a', 'video/mp4' => 'm4a',
            'audio/aac' => 'aac',
            'audio/webm', 'video/webm' => 'webm',
            default => 'mp3',
        };
    }

    private function deleteStoredAsset(?string $url): void
    {
        app(StoredAssetCleaner::class)->deleteFromUrl($url);
    }
}
