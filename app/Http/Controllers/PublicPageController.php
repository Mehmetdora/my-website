<?php

namespace App\Http\Controllers;

use App\Models\LifePost;
use App\Models\Post;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicPageController extends Controller
{
    public function home()
    {
        $posts = Post::query()
            ->with('tags')
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map->toViewArray()
            ->all();

        $projects = Project::query()
            ->with('tags')
            ->visible()
            ->where('featured', true)
            ->latest()
            ->take(3)
            ->get()
            ->map->toViewArray()
            ->all();

        return view('pages.home', [
            'posts' => $posts,
            'projects' => $projects,
            'title' => 'Home',
        ]);
    }

    public function about()
    {
        return view('pages.about', ['title' => 'About']);
    }

    public function blogIndex(Request $request)
    {
        $activeTag = strtolower((string) $request->query('tag', ''));

        $posts = Post::query()
            ->with('tags')
            ->published()
            ->when($activeTag, fn ($query) => $query->whereHas('tags', fn ($tagQuery) => $tagQuery->whereRaw('lower(slug) = ?', [$activeTag])))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $posts->setCollection($posts->getCollection()->map->toViewArray());

        return view('pages.blog-index', [
            'posts' => $posts,
            'activeTag' => $activeTag,
            'title' => 'Blog',
        ]);
    }

    public function blogShow(string $slug)
    {
        $postModel = Post::query()
            ->with('tags')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
        $post = $postModel->toViewArray();

        $related = Post::query()
            ->with('tags')
            ->published()
            ->whereKeyNot($postModel->id)
            ->whereHas('tags', fn ($query) => $query->whereIn('tags.id', $postModel->tags->pluck('id')))
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map->toViewArray()
            ->all();

        return view('pages.blog-detail', [
            'post' => $post,
            'related' => $related,
            'title' => $post['title'],
            'description' => $post['summary'],
        ]);
    }

    public function projectsIndex()
    {
        $projects = Project::query()
            ->with('tags')
            ->visible()
            ->latest()
            ->paginate(12);

        $projects->setCollection($projects->getCollection()->map->toViewArray());

        return view('pages.projects-index', [
            'projects' => $projects,
            'title' => 'Projects',
        ]);
    }

    public function projectShow(string $slug)
    {
        $projectModel = Project::query()
            ->with('tags')
            ->visible()
            ->where('slug', $slug)
            ->firstOrFail();
        $project = $projectModel->toViewArray();

        $related = Project::query()
            ->with('tags')
            ->visible()
            ->whereKeyNot($projectModel->id)
            ->whereHas('tags', fn ($query) => $query->whereIn('tags.id', $projectModel->tags->pluck('id')))
            ->latest()
            ->take(3)
            ->get()
            ->map->toViewArray()
            ->all();

        return view('pages.project-detail', [
            'project' => $project,
            'related' => $related,
            'title' => $project['title'],
            'description' => $project['summary'],
        ]);
    }

    public function life(Request $request)
    {
        $lifePosts = LifePost::query()
            ->with('images')
            ->visible()
            ->orderByDesc('published_at')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $lifePosts->setCollection($lifePosts->getCollection()->map->toViewArray());

        if ($request->expectsJson()) {
            return response()->json([
                'cards_html' => $lifePosts->getCollection()
                    ->map(fn (array $item): string => view('partials.life-card', ['item' => $item])->render())
                    ->implode(''),
                'modals_html' => $lifePosts->getCollection()
                    ->map(fn (array $item): string => view('partials.life-modal', ['item' => $item])->render())
                    ->implode(''),
                'next_page_url' => $lifePosts->nextPageUrl(),
            ]);
        }

        return view('pages.life', [
            'lifePosts' => $lifePosts,
            'title' => 'My Life',
        ]);
    }

    public function cv()
    {
        return view('pages.cv', ['title' => 'Resume']);
    }

    public function cvPdf()
    {
        $site = SiteSetting::current()->site;
        $pdfUrl = $site['cv_pdf_url'] ?? null;

        if (is_string($pdfUrl) && str_starts_with($pdfUrl, '/storage/')) {
            $path = substr($pdfUrl, strlen('/storage/'));

            if (Storage::disk('public')->exists($path)) {
                return response()->file(Storage::disk('public')->path($path), [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="'.($site['cv_pdf_name'] ?? 'cv.pdf').'"',
                ]);
            }
        }

        return redirect()->route('cv');
    }

    public function tagShow(string $slug)
    {
        $slug = strtolower($slug);
        $tag = Tag::query()->whereRaw('lower(slug) = ?', [$slug])->firstOrFail();

        $posts = Post::query()
            ->with('tags')
            ->published()
            ->whereHas('tags', fn ($query) => $query->whereKey($tag->id))
            ->latest('published_at')
            ->get()
            ->map->toViewArray()
            ->all();

        $projects = Project::query()
            ->with('tags')
            ->visible()
            ->whereHas('tags', fn ($query) => $query->whereKey($tag->id))
            ->latest()
            ->get()
            ->map->toViewArray()
            ->all();

        return view('pages.tag', [
            'tag' => $tag->toViewArray(),
            'posts' => $posts,
            'projects' => $projects,
            'title' => '#'.$tag->slug,
        ]);
    }
}
