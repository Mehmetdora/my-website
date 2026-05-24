<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Support\CroppedImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        return view('admin.blog.index', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Blog Yönetimi',
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
            'title' => 'Yeni Blog',
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

        return redirect()->route('admin.blog.edit', $post->slug)->with('status', 'Blog oluşturuldu.');
    }

    public function edit(string $slug)
    {
        $post = Post::query()->with('tags')->where('slug', $slug)->firstOrFail();

        return view('admin.blog.form', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Blog Düzenle',
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

        return redirect()->route('admin.blog.edit', $post->slug)->with('status', 'Blog kaydedildi.');
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
            'cover_image.uploaded' => 'Kapak görseli sunucuya yüklenemedi. Bu genelde dosya boyutu 4 MB sınırını veya sunucunun upload limitini aştığında olur; lütfen daha küçük bir JPG, PNG, GIF veya WebP dosya seç.',
            'cover_image.image' => 'Kapak görseli geçerli bir görsel dosyası değil.',
            'cover_image.mimes' => 'Kapak görseli sadece JPG, PNG, GIF veya WebP formatında olabilir.',
            'cover_image.max' => 'Kapak görseli çok büyük. En fazla 4 MB olabilir.',
            'cover_crop.json' => 'Kapak görseli kırpma bilgisi doğru gönderilemedi. Lütfen görseli yeniden seçip kadrajla.',
            'delete_cover_image.boolean' => 'Kapak görseli silme seçimi geçersiz gönderildi. Sayfayı yenileyip tekrar dene.',
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

        abort_if($exists, 422, 'Bu blog slug değeri zaten kullanılıyor.');
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

        abort_if($expected->count() !== $found, 422, 'Seçilen taglerden biri bulunamadı.');
    }

    private function deleteStoredCover(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/storage/blog/')) {
            return;
        }

        Storage::disk('public')->delete(substr($url, strlen('/storage/')));
    }
}
