<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Support\CroppedImageStorage;
use App\Support\StoredAssetCleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        return view('admin.blog.index', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Blog Management',
            'site' => SiteSetting::current()->site,
            'posts' => Post::query()->with('tags')->latest()->get()->map->toViewArray()->all(),
            'tags' => Tag::query()->orderBy('name')->get()->map->toViewArray()->all(),
        ]);
    }

    public function create()
    {
        return view('admin.blog.form', [
            'admin' => true,
            'noindex' => true,
            'title' => 'New Blog',
            'site' => SiteSetting::current()->site,
            'post' => null,
            'tags' => Tag::query()->orderBy('name')->get()->map->toViewArray()->all(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('cover_image')) {
            $data['cover_url'] = app(CroppedImageStorage::class)->store(
                $request->file('cover_image'),
                'blog',
                $request->input('cover_crop')
            );
        }

        $post = Post::query()->create($data);
        $this->syncTags($post, $request->input('tags', []));

        return redirect()->route('admin.blog.edit', $post->slug)->with('status', 'Blog created.');
    }

    public function edit(string $slug)
    {
        $post = Post::query()->with('tags')->where('slug', $slug)->firstOrFail();

        return view('admin.blog.form', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Edit Blog',
            'site' => SiteSetting::current()->site,
            'post' => $post->toViewArray(),
            'tags' => Tag::query()->orderBy('name')->get()->map->toViewArray()->all(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $post = Post::query()->where('slug', $slug)->firstOrFail();
        $data = $this->validatedData($request, $post->id, $post);

        if ($request->boolean('delete_cover_image')) {
            $this->deleteStoredCover($post->cover_url);
            $data['cover_url'] = null;
        }

        if ($request->hasFile('cover_image')) {
            $this->deleteStoredCover($post->cover_url);
            $data['cover_url'] = app(CroppedImageStorage::class)->store(
                $request->file('cover_image'),
                'blog',
                $request->input('cover_crop')
            );
        }

        $post->update($data);
        $this->syncTags($post, $request->input('tags', []));

        return redirect()->route('admin.blog.edit', $post->slug)->with('status', 'Blog saved.');
    }

    public function destroy(string $slug)
    {
        $post = Post::query()->where('slug', $slug)->firstOrFail();

        $this->deleteStoredCover($post->cover_url);
        app(StoredAssetCleaner::class)->deleteImagesFromHtml($post->content_html);
        $post->tags()->detach();
        $post->delete();

        return redirect()->route('admin.blog.index')->with('status', 'Blog deleted.');
    }

    private function validatedData(Request $request, ?int $ignoreId = null, ?Post $post = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
            'visibility' => ['required', 'in:public,hidden,private'],
            'published_at' => ['nullable', 'date'],
            'reading_time' => ['required', 'integer', 'min:1', 'max:999'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
            'cover_crop' => ['nullable', 'json'],
            'delete_cover_image' => ['nullable', 'boolean'],
            'content_html' => ['nullable', 'string'],
            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['required', 'string'],
        ], [
            'cover_image.uploaded' => 'The cover image could not be uploaded. This usually means the file exceeded the 4 MB limit or the server upload limit; please choose a smaller JPG, PNG, GIF, or WebP file.',
            'cover_image.image' => 'The cover image is not a valid image file.',
            'cover_image.mimes' => 'The cover image must be JPG, PNG, GIF, or WebP.',
            'cover_image.max' => 'The cover image is too large. It can be at most 4 MB.',
            'cover_crop.json' => 'The cover crop data could not be submitted correctly. Please choose and crop the image again.',
            'delete_cover_image.boolean' => 'The cover image delete option was submitted incorrectly. Please refresh the page and try again.',
        ]);

        $slug = Str::slug(strtolower($validated['slug'] ?: $validated['title']));
        $this->ensureUniqueSlug($slug, $ignoreId);
        $this->ensureTagsExist($validated['tags'] ?? []);

        return [
            'title' => $validated['title'],
            'slug' => $slug,
            'summary' => $validated['summary'] ?? null,
            'status' => $validated['status'],
            'visibility' => $validated['visibility'],
            'published_at' => $validated['published_at'] ?? null,
            'reading_time' => $validated['reading_time'],
            'cover_url' => $post?->cover_url,
            'cover_alt' => $validated['title'],
            'content_html' => sanitize_content_html($validated['content_html'] ?? ''),
        ];
    }

    private function ensureUniqueSlug(string $slug, ?int $ignoreId): void
    {
        $exists = Post::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        abort_if($exists, 422, 'This blog slug is already in use.');
    }

    private function syncTags(Post $post, array $slugs): void
    {
        $ids = Tag::query()
            ->whereIn('slug', collect($slugs)->map(fn ($slug) => strtolower((string) $slug))->filter())
            ->pluck('id');

        $post->tags()->sync($ids);
    }

    private function ensureTagsExist(array $slugs): void
    {
        $expected = collect($slugs)->map(fn ($slug) => strtolower((string) $slug))->filter()->unique()->values();
        $found = Tag::query()->whereIn('slug', $expected)->count();

        abort_if($expected->count() !== $found, 422, 'One of the selected tags could not be found.');
    }

    private function deleteStoredCover(?string $url): void
    {
        app(StoredAssetCleaner::class)->deleteFromUrl($url);
    }
}
