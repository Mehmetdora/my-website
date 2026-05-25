<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class StoredAssetCleaner
{
    public function deleteFromUrl(?string $url): void
    {
        $path = $this->storagePathFromUrl($url);

        if ($path !== null) {
            Storage::disk('public')->delete($path);
        }
    }

    public function deleteImagesFromHtml(?string $html): void
    {
        foreach ($this->imageUrlsFromHtml($html) as $url) {
            $this->deleteFromUrl($url);
        }
    }

    /**
     * @return array<int, string>
     */
    private function imageUrlsFromHtml(?string $html): array
    {
        if (! is_string($html) || trim($html) === '') {
            return [];
        }

        preg_match_all('/<img\b[^>]*\bsrc\s*=\s*([\'"])(.*?)\1/i', $html, $matches);

        return collect($matches[2] ?? [])
            ->map(fn (string $url): string => html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function storagePathFromUrl(?string $url): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/storage/')) {
            $path = substr($url, strlen('/storage/'));
        } elseif (preg_match('#/storage/([^?#]+)#', $url, $matches)) {
            $path = $matches[1];
        } else {
            return null;
        }

        $path = ltrim(rawurldecode($path), '/');

        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }
}
