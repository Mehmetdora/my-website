<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['post_type', 'excerpt', 'location', 'visibility', 'audio_url', 'audio_name', 'audio_mime', 'audio_size', 'published_at'])]
class LifePost extends Model
{
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(LifePostImage::class)->orderBy('sort_order');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visibility', 'public')->whereNotNull('published_at');
    }

    public function toViewArray(): array
    {
        return [
            'id' => (string) $this->id,
            'type' => $this->post_type ?: 'image',
            'excerpt' => $this->excerpt,
            'location' => $this->location,
            'published_at' => optional($this->published_at)->toDateString(),
            'images' => $this->images->map->toViewArray()->values()->all(),
            'audio' => $this->audio_url ? [
                'url' => $this->audio_url,
                'name' => $this->audio_name,
                'mime' => $this->audio_mime,
                'size' => $this->audio_size,
            ] : null,
        ];
    }
}
