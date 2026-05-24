<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'title',
    'slug',
    'summary',
    'cover_url',
    'cover_alt',
    'status',
    'visibility',
    'reading_time',
    'published_at',
    'content_html',
])]
class Post extends Model
{
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'reading_time' => 'integer',
        ];
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->whereNotNull('published_at');
    }

    public function toViewArray(): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'cover' => [
                'id' => 'post-cover-'.$this->id,
                'url' => $this->cover_url ?: '/profile.svg',
                'alt' => $this->cover_alt ?: $this->title,
            ],
            'tags' => $this->tags->pluck('slug')->values()->all(),
            'status' => $this->status,
            'visibility' => $this->visibility,
            'published_at' => optional($this->published_at)->toDateString(),
            'updated_at' => optional($this->updated_at)->toDateString(),
            'reading_time' => $this->reading_time ?: 1,
            'content' => [],
            'content_html' => $this->content_html,
        ];
    }
}
