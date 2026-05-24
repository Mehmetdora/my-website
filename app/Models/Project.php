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
    'featured',
    'github',
    'technologies',
    'content_html',
])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'technologies' => 'array',
        ];
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visibility', 'public')->where('status', '!=', 'archived');
    }

    public function toViewArray(): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'cover' => [
                'id' => 'project-cover-'.$this->id,
                'url' => $this->cover_url ?: '/profile.svg',
                'alt' => $this->cover_alt ?: $this->title,
            ],
            'technologies' => $this->technologies ?: [],
            'status' => $this->status,
            'visibility' => $this->visibility,
            'featured' => $this->featured,
            'tags' => $this->tags->pluck('slug')->values()->all(),
            'github' => $this->github,
            'content' => [],
            'content_html' => $this->content_html,
        ];
    }
}
