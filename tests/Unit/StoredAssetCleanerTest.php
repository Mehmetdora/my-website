<?php

namespace Tests\Unit;

use App\Support\StoredAssetCleaner;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoredAssetCleanerTest extends TestCase
{
    public function test_it_deletes_public_storage_files_from_full_urls(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/demo-image.jpg', 'demo');

        app(StoredAssetCleaner::class)->deleteFromUrl('https://example.com/storage/posts/demo-image.jpg?version=1#hero');

        Storage::disk('public')->assertMissing('posts/demo-image.jpg');
    }
}
