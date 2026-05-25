<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class CroppedImageStorage
{
    public function store(
        UploadedFile $file,
        string $directory,
        ?string $cropJson = null,
        float $aspectRatio = 2.0
    ): string {
        $image = Image::decodePath($file->getRealPath());
        $width = $image->width();
        $height = $image->height();

        [$cropX, $cropY, $cropWidth, $cropHeight] = $this->resolveCropBox(
            $this->parseCrop($cropJson),
            $width,
            $height,
            $aspectRatio
        );

        $extension = $this->extensionFor($file);
        $path = trim($directory, '/').'/'.Str::uuid().'.'.$extension;
        $absolutePath = Storage::disk('public')->path($path);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }

        $image
            ->crop($cropWidth, $cropHeight, $cropX, $cropY)
            ->save($absolutePath, quality: 88);

        return '/storage/'.ltrim($path, '/');
    }

    private function parseCrop(?string $cropJson): ?array
    {
        if (! $cropJson) {
            return null;
        }

        $crop = json_decode($cropJson, true);

        if (! is_array($crop)) {
            return null;
        }

        foreach (['x', 'y', 'width', 'height'] as $key) {
            if (! isset($crop[$key]) || ! is_numeric($crop[$key])) {
                return null;
            }
        }

        return [
            'x' => (float) $crop['x'],
            'y' => (float) $crop['y'],
            'width' => (float) $crop['width'],
            'height' => (float) $crop['height'],
        ];
    }

    private function resolveCropBox(?array $crop, int $imageWidth, int $imageHeight, float $aspectRatio): array
    {
        if (! $crop) {
            $crop = $this->centerCrop($imageWidth, $imageHeight, $aspectRatio);
        }

        $cropWidth = max(1.0, min($crop['width'], $imageWidth));
        $cropHeight = max(1.0, min($crop['height'], $imageHeight));
        $centerX = max(0.0, min($crop['x'] + ($cropWidth / 2), $imageWidth));
        $centerY = max(0.0, min($crop['y'] + ($cropHeight / 2), $imageHeight));

        if (($cropWidth / $cropHeight) > $aspectRatio) {
            $cropWidth = $cropHeight * $aspectRatio;
        } else {
            $cropHeight = $cropWidth / $aspectRatio;
        }

        if ($cropWidth > $imageWidth) {
            $cropWidth = $imageWidth;
            $cropHeight = $cropWidth / $aspectRatio;
        }

        if ($cropHeight > $imageHeight) {
            $cropHeight = $imageHeight;
            $cropWidth = $cropHeight * $aspectRatio;
        }

        $cropWidth = (int) max(1, floor($cropWidth));
        $cropHeight = (int) max(1, floor($cropHeight));
        $cropX = (int) max(0, min(round($centerX - ($cropWidth / 2)), $imageWidth - $cropWidth));
        $cropY = (int) max(0, min(round($centerY - ($cropHeight / 2)), $imageHeight - $cropHeight));

        return [$cropX, $cropY, $cropWidth, $cropHeight];
    }

    private function centerCrop(int $imageWidth, int $imageHeight, float $aspectRatio): array
    {
        $cropWidth = $imageWidth;
        $cropHeight = $imageWidth / $aspectRatio;

        if ($cropHeight > $imageHeight) {
            $cropHeight = $imageHeight;
            $cropWidth = $imageHeight * $aspectRatio;
        }

        return [
            'x' => ($imageWidth - $cropWidth) / 2,
            'y' => ($imageHeight - $cropHeight) / 2,
            'width' => $cropWidth,
            'height' => $cropHeight,
        ];
    }

    private function extensionFor(UploadedFile $file): string
    {
        return match ($file->getMimeType()) {
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }
}
