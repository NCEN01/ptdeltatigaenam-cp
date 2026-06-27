<?php

namespace App\Filament\Forms\Components;

use App\Services\MediaService;
use Filament\Forms\Components\FileUpload;

/**
 * Drop-in FileUpload that routes every upload through MediaService:
 * validation, resize, WebP conversion, responsive variants, EXIF strip,
 * SVG sanitization and variant cleanup on delete.
 *
 * Usage: MediaUpload::for('image', 'hero', 'banners')
 */
class MediaUpload extends FileUpload
{
    protected string $mediaProfile = 'service';

    protected string $mediaDirectory = 'uploads';

    public static function for(string $name, string $profile, string $directory = 'uploads'): static
    {
        $component = static::make($name);
        $component->mediaProfile = $profile;
        $component->mediaDirectory = $directory;

        return $component;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $spec = config("media.profiles.{$this->mediaProfile}");

        $this->image()
            ->disk(config('media.disk', 'public'))
            ->maxSize(($spec['max_upload_kb'] ?? 4096))
            ->acceptedFileTypes($this->acceptedTypes($spec))
            ->helperText($this->dimensionHint($spec))
            ->saveUploadedFileUsing(fn ($file) => app(MediaService::class)->process(
                $file, $this->mediaProfile, $this->mediaDirectory,
            ))
            ->deleteUploadedFileUsing(fn (?string $file) => app(MediaService::class)->delete($file));
    }

    private function acceptedTypes(?array $spec): array
    {
        $map = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
            'webp' => 'image/webp', 'svg' => 'image/svg+xml',
        ];

        return array_values(array_unique(array_map(
            fn ($f) => $map[$f] ?? 'image/*',
            $spec['formats'] ?? ['jpg', 'png', 'webp'],
        )));
    }

    private function dimensionHint(?array $spec): string
    {
        if (! $spec) {
            return '';
        }

        return "Disarankan {$spec['width']}×{$spec['height']} px, maks {$spec['max_upload_kb']} KB. "
            .'Otomatis dioptimasi & dikonversi ke WebP.';
    }
}
