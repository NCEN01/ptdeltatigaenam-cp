<?php

namespace Tests\Feature;

use App\Services\MediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MediaServiceTest extends TestCase
{
    private MediaService $media;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        config()->set('media.disk', 'public');
        $this->media = new MediaService();
    }

    public function test_valid_image_becomes_webp_with_responsive_variants(): void
    {
        $file = UploadedFile::fake()->image('hero.jpg', 1920, 800);

        $path = $this->media->process($file, 'hero', 'banners');

        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        // At least one smaller responsive variant was generated.
        $base = substr($path, 0, -strlen('.webp'));
        $variants = collect(Storage::disk('public')->files('banners'))
            ->filter(fn ($f) => str_starts_with($f, $base.'-'));
        $this->assertNotEmpty($variants, 'Expected responsive variants to be generated.');
    }

    public function test_oversized_file_is_rejected(): void
    {
        config()->set('media.profiles.hero.max_upload_kb', 1); // 1 KB cap
        $file = UploadedFile::fake()->image('hero.jpg', 1920, 800);

        $this->expectException(ValidationException::class);
        $this->media->process($file, 'hero', 'banners');
    }

    public function test_disallowed_type_is_rejected(): void
    {
        // 'avatar' profile does not accept svg.
        $file = UploadedFile::fake()->create('evil.svg', 10, 'image/svg+xml');

        $this->expectException(ValidationException::class);
        $this->media->process($file, 'avatar', 'avatars');
    }
}
