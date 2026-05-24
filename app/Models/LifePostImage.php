<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['life_post_id', 'url', 'alt', 'sort_order'])]
class LifePostImage extends Model
{
    public function lifePost(): BelongsTo
    {
        return $this->belongsTo(LifePost::class);
    }

    public function toViewArray(): array
    {
        return [
            'id' => (string) $this->id,
            'url' => $this->url,
            'alt' => $this->alt,
        ];
    }
}
