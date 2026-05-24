<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['site', 'home', 'about'])]
class SiteSetting extends Model
{
    protected function casts(): array
    {
        return [
            'site' => 'array',
            'home' => 'array',
            'about' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->first()
            ?? static::query()->create([
                'site' => config('content.site'),
                'home' => config('content.home'),
                'about' => config('content.about'),
            ]);
    }
}
