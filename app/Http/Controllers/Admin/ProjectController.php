<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Support\StoredAssetCleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        return view('admin.projects.index', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Project Management',
            'site' => SiteSetting::current()->site,
            'projects' => Project::query()->with('tags')->latest()->get()->map->toViewArray()->all(),
            'tags' => Tag::query()->orderBy('name')->get()->map->toViewArray()->all(),
        ]);
    }

    public function create()
    {
        return view('admin.projects.form', [
            'admin' => true,
            'noindex' => true,
            'title' => 'New Project',
            'site' => SiteSetting::current()->site,
            'project' => null,
            'tags' => Tag::query()->orderBy('name')->get()->map->toViewArray()->all(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $project = Project::query()->create($this->validatedData($request));
        $this->syncTags($project, $request->input('tags', []));

        return redirect()->route('admin.projects.edit', $project->slug)->with('status', 'Project created.');
    }

    public function edit(string $slug)
    {
        $project = Project::query()->with('tags')->where('slug', $slug)->firstOrFail();

        return view('admin.projects.form', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Edit Project',
            'site' => SiteSetting::current()->site,
            'project' => $project->toViewArray(),
            'tags' => Tag::query()->orderBy('name')->get()->map->toViewArray()->all(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $project = Project::query()->where('slug', $slug)->firstOrFail();
        $project->update($this->validatedData($request, $project->id));
        $this->syncTags($project, $request->input('tags', []));

        return redirect()->route('admin.projects.edit', $project->slug)->with('status', 'Project saved.');
    }

    public function destroy(string $slug)
    {
        $project = Project::query()->where('slug', $slug)->firstOrFail();

        app(StoredAssetCleaner::class)->deleteImagesFromHtml($project->content_html);
        $project->tags()->detach();
        $project->delete();

        return redirect()->route('admin.projects.index')->with('status', 'Project deleted.');
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'status' => ['required', 'in:planned,in-progress,completed,archived'],
            'visibility' => ['required', 'in:public,hidden,private'],
            'featured' => ['required', 'boolean'],
            'github' => ['nullable', 'string'],
            'technologies' => ['nullable', 'string'],
            'content_html' => ['nullable', 'string'],
            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['required', 'string'],
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
            'featured' => (bool) $validated['featured'],
            'github' => $validated['github'] ?? null,
            'cover_url' => null,
            'cover_alt' => $validated['title'],
            'technologies' => collect(explode("\n", $validated['technologies'] ?? ''))
                ->map(fn ($tech) => trim($tech))
                ->filter()
                ->values()
                ->all(),
            'content_html' => sanitize_content_html($validated['content_html'] ?? ''),
        ];
    }

    private function ensureUniqueSlug(string $slug, ?int $ignoreId): void
    {
        $exists = Project::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        abort_if($exists, 422, 'This project slug is already in use.');
    }

    private function syncTags(Project $project, array $slugs): void
    {
        $ids = Tag::query()
            ->whereIn('slug', collect($slugs)->map(fn ($slug) => strtolower((string) $slug))->filter())
            ->pluck('id');

        $project->tags()->sync($ids);
    }

    private function ensureTagsExist(array $slugs): void
    {
        $expected = collect($slugs)->map(fn ($slug) => strtolower((string) $slug))->filter()->unique()->values();
        $found = Tag::query()->whereIn('slug', $expected)->count();

        abort_if($expected->count() !== $found, 422, 'One of the selected tags could not be found.');
    }
}
