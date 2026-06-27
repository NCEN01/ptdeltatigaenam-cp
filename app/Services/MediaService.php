<?php

namespace App\Services;

use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Spatie\ImageOptimizer\OptimizerChainFactory;

/**
 * Shared upload pipeline (PRD §3.10 & §8.12):
 *  validate -> resize -> compress -> WebP -> responsive variants,
 *  with hashed filenames, EXIF stripped and SVG sanitized.
 */
class MediaService
{
    public function __construct(private readonly ImageManager $manager = new ImageManager(new Driver())) {}

    /**
     * Process an uploaded file against a media profile and return the stored
     * path (relative to the public disk). Generates responsive WebP variants.
     *
     * @throws ValidationException
     */
    public function process(UploadedFile $file, string $profile, string $directory = 'uploads'): string
    {
        $spec = $this->spec($profile);
        $this->validate($file, $spec, $profile);

        $disk = config('media.disk', 'public');
        $directory = trim($directory, '/');
        $name = $this->randomName();

        // Vectors: sanitize and store as-is (never executed, no rasterizing).
        if ($file->getMimeType() === 'image/svg+xml') {
            $path = "{$directory}/{$name}.svg";
            Storage::disk($disk)->put($path, $this->sanitizeSvg($file->getRealPath()));

            return $path;
        }

        // Raster: decode (this drops EXIF), crop/fit to target, encode WebP.
        $image = $this->manager->read($file->getRealPath());

        $this->fit($image, $spec);
        $mainPath = "{$directory}/{$name}.webp";
        Storage::disk($disk)->put($mainPath, (string) $image->toWebp($this->qualityFor($image, $spec)));
        $this->optimize($disk, $mainPath);

        // Responsive variants ({name}-{width}.webp) for srcset.
        foreach ($spec['responsive'] ?? [] as $width) {
            if ($width >= $spec['width']) {
                continue;
            }
            $variant = $this->manager->read($file->getRealPath());
            $this->fit($variant, ['width' => $width, 'height' => (int) round($width * $spec['height'] / $spec['width']), 'fit' => $spec['fit']]);
            $variantPath = "{$directory}/{$name}-{$width}.webp";
            Storage::disk($disk)->put($variantPath, (string) $variant->toWebp($this->qualityFor($variant, $spec)));
            $this->optimize($disk, $variantPath);
        }

        return $mainPath;
    }

    /**
     * Build a srcset string for a stored main image path, using its variants.
     */
    public function srcset(string $mainPath, string $profile): string
    {
        $spec = $this->spec($profile);
        $disk = config('media.disk', 'public');
        $base = Str::beforeLast($mainPath, '.webp');
        $dir = Str::beforeLast($mainPath, '/');
        $entries = [];

        foreach ($spec['responsive'] ?? [] as $width) {
            $candidate = $width >= $spec['width'] ? $mainPath : "{$base}-{$width}.webp";
            if (Storage::disk($disk)->exists($candidate)) {
                $entries[] = Storage::disk($disk)->url($candidate)." {$width}w";
            }
        }

        return implode(', ', $entries);
    }

    /**
     * Delete a stored image and all its responsive variants.
     */
    public function delete(?string $mainPath): void
    {
        if (blank($mainPath)) {
            return;
        }

        $disk = config('media.disk', 'public');
        Storage::disk($disk)->delete($mainPath);

        $base = Str::beforeLast($mainPath, '.webp');
        foreach (Storage::disk($disk)->files(Str::beforeLast($mainPath, '/')) as $f) {
            if (Str::startsWith($f, $base.'-')) {
                Storage::disk($disk)->delete($f);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    public function validate(UploadedFile $file, array $spec, string $profile): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        $allowedMimes = array_merge(
            in_array('svg', $spec['formats'], true) ? config('media.vector_mimes') : [],
            config('media.raster_mimes'),
        );

        if (! in_array($extension, $spec['formats'], true) || ! in_array($mime, $allowedMimes, true)) {
            throw ValidationException::withMessages([
                'file' => "Tipe berkas tidak diizinkan untuk profil '{$profile}'. Diperbolehkan: ".implode(', ', $spec['formats']).'.',
            ]);
        }

        $maxBytes = $spec['max_upload_kb'] * 1024;
        if ($file->getSize() > $maxBytes) {
            throw ValidationException::withMessages([
                'file' => "Ukuran berkas melebihi batas {$spec['max_upload_kb']} KB untuk profil '{$profile}'.",
            ]);
        }
    }

    public function spec(string $profile): array
    {
        $spec = config("media.profiles.{$profile}");

        if (! $spec) {
            throw new \InvalidArgumentException("Profil media tidak dikenal: {$profile}");
        }

        return $spec;
    }

    private function fit($image, array $spec): void
    {
        if (($spec['fit'] ?? 'cover') === 'contain') {
            // Keep entire image within the box without cropping or upscaling.
            $image->scaleDown($spec['width'], $spec['height']);
        } else {
            $image->cover($spec['width'], $spec['height']);
        }
    }

    private function qualityFor($image, array $spec): int
    {
        // Start high; the optimizer + WebP keep files near the KB target.
        return 82;
    }

    private function optimize(string $disk, string $path): void
    {
        try {
            $full = Storage::disk($disk)->path($path);
            OptimizerChainFactory::create()->optimize($full);
        } catch (\Throwable $e) {
            // Binary optimizers are optional; WebP encoding already compresses.
            report($e);
        }
    }

    private function sanitizeSvg(string $path): string
    {
        $sanitizer = new Sanitizer();
        $sanitizer->removeRemoteReferences(true);
        $clean = $sanitizer->sanitize(file_get_contents($path));

        return $clean !== false ? $clean : '';
    }

    private function randomName(): string
    {
        return Str::lower(Str::random(40));
    }
}
