<?php

namespace Database\Seeders;

use App\Models\LifePost;
use App\Models\Post;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@personal-website.local'],
            [
                'name' => 'Personal Website Admin',
                'password' => Hash::make('password'),
            ]
        );

        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'site' => config('content.site'),
                'home' => config('content.home'),
                'about' => config('content.about'),
            ]
        );

        $tagIds = [];
        foreach (config('content.tags') as $tag) {
            $model = Tag::query()->updateOrCreate(
                ['slug' => strtolower($tag['slug'])],
                ['name' => $tag['name']]
            );
            $tagIds[$model->slug] = $model->id;
        }

        foreach (config('content.posts') as $item) {
            $post = Post::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'summary' => $item['summary'] ?? null,
                    'cover_url' => $item['cover']['url'] ?? null,
                    'cover_alt' => $item['cover']['alt'] ?? $item['title'],
                    'status' => $item['status'] ?? 'published',
                    'visibility' => $item['visibility'] ?? 'public',
                    'reading_time' => $item['reading_time'] ?? 1,
                    'published_at' => $item['published_at'] ?? null,
                    'content_html' => sanitize_content_html($this->blocksToHtml($item['content'] ?? [])),
                ]
            );

            $post->tags()->sync(collect($item['tags'] ?? [])->map(fn ($slug) => $tagIds[strtolower($slug)] ?? null)->filter()->values());
        }

        foreach (config('content.projects') as $item) {
            $project = Project::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'summary' => $item['summary'] ?? null,
                    'cover_url' => $item['cover']['url'] ?? null,
                    'cover_alt' => $item['cover']['alt'] ?? $item['title'],
                    'status' => $item['status'] ?? 'planned',
                    'visibility' => $item['visibility'] ?? 'public',
                    'featured' => $item['featured'] ?? false,
                    'github' => $item['github'] ?? null,
                    'technologies' => $item['technologies'] ?? [],
                    'content_html' => sanitize_content_html($this->blocksToHtml($item['content'] ?? [])),
                ]
            );

            $project->tags()->sync(collect($item['tags'] ?? [])->map(fn ($slug) => $tagIds[strtolower($slug)] ?? null)->filter()->values());
        }

        foreach (config('content.life') as $index => $item) {
            $lifePost = LifePost::query()->updateOrCreate(
                ['id' => $index + 1],
                [
                    'excerpt' => $item['excerpt'],
                    'location' => $item['location'] ?? null,
                    'visibility' => 'public',
                    'published_at' => $item['published_at'] ?? null,
                ]
            );

            $lifePost->images()->delete();
            foreach ($item['images'] ?? [] as $imageIndex => $image) {
                $lifePost->images()->create([
                    'url' => $image['url'],
                    'alt' => $image['alt'] ?? $item['excerpt'],
                    'sort_order' => $imageIndex,
                ]);
            }
        }
    }

    private function blocksToHtml(array $blocks): string
    {
        return collect($blocks)->map(function (array $block): string {
            return match ($block['type'] ?? 'paragraph') {
                'heading' => '<h2>'.e($block['text'] ?? '').'</h2>',
                'quote' => '<blockquote>'.e($block['text'] ?? '').'</blockquote>',
                'callout' => '<blockquote><strong>'.e($block['title'] ?? 'Not').'</strong><br>'.e($block['text'] ?? '').'</blockquote>',
                'code' => '<pre>'.e($block['code'] ?? '').'</pre>',
                'list' => '<ul>'.collect($block['items'] ?? [])->map(fn ($item) => '<li>'.e($item).'</li>')->implode('').'</ul>',
                default => '<p>'.e($block['text'] ?? '').'</p>',
            };
        })->implode('');
    }
}
