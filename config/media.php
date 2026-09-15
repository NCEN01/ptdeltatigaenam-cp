<?php

/**
 * Media standards (PRD §3.10). Each profile defines target output dimensions,
 * crop strategy, output quality target, accepted input formats, the raw upload
 * size cap (rejection threshold) and responsive widths for srcset generation.
 *
 * fit: 'cover'   -> crop to fill exact ratio (hero, banner, thumbnail, photos)
 *      'contain' -> fit within box, keep full image (logos, avatars)
 */
return [
    'disk' => 'public',

    'raster_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
    'vector_mimes' => ['image/svg+xml'],

    'profiles' => [
        'hero' => [
            // Full-screen hero — master at 2400px so it stays crisp on 2K/retina displays.
            'width' => 2400, 'height' => 1000, 'fit' => 'cover',
            'target_kb' => 700, 'max_upload_kb' => 12288, 'quality' => 90,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [2400, 1920, 1280, 768, 480],
        ],
        'hero_mobile' => [
            'width' => 800, 'height' => 1000, 'fit' => 'cover',
            'target_kb' => 300, 'max_upload_kb' => 6144,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [800, 480],
        ],
        'banner' => [
            // Banners render as a full-width hero, so the master must be HD (was 1200 → blurry
            // when upscaled to a 1920px+ viewport). 2000px stays crisp on 1080p/retina laptops;
            // srcset serves the small variants to phones so speed stays safe.
            'width' => 2000, 'height' => 700, 'fit' => 'cover',
            'target_kb' => 600, 'max_upload_kb' => 12288, 'quality' => 88,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [2000, 1600, 1280, 768, 480],
        ],
        'thumbnail' => [
            'width' => 800, 'height' => 600, 'fit' => 'cover',
            'target_kb' => 250, 'max_upload_kb' => 4096,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [800, 480],
        ],
        'service' => [
            'width' => 1200, 'height' => 800, 'fit' => 'cover',
            'target_kb' => 350, 'max_upload_kb' => 6144,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [1200, 768, 480],
        ],
        'blog' => [
            'width' => 1200, 'height' => 630, 'fit' => 'cover',
            'target_kb' => 300, 'max_upload_kb' => 6144,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [1200, 768, 480],
        ],
        'blog_banner' => [
            'width' => 2000, 'height' => 625, 'fit' => 'cover',
            'target_kb' => 550, 'max_upload_kb' => 10240, 'quality' => 88,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [2000, 1600, 1200, 768],
        ],
        'portfolio' => [
            'width' => 1200, 'height' => 800, 'fit' => 'cover',
            'target_kb' => 350, 'max_upload_kb' => 6144,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [1200, 768, 480],
        ],
        'logo' => [
            'width' => 400, 'height' => 200, 'fit' => 'contain',
            'target_kb' => 100, 'max_upload_kb' => 2048,
            'formats' => ['png', 'svg', 'webp', 'jpg', 'jpeg'],
            'responsive' => [400],
        ],
        'avatar' => [
            'width' => 200, 'height' => 200, 'fit' => 'cover',
            'target_kb' => 80, 'max_upload_kb' => 2048,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [200],
        ],
        'site_logo' => [
            'width' => 240, 'height' => 80, 'fit' => 'contain',
            'target_kb' => 100, 'max_upload_kb' => 2048,
            'formats' => ['png', 'svg', 'webp'],
            'responsive' => [240],
        ],
    ],
];
