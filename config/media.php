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
            'width' => 1920, 'height' => 800, 'fit' => 'cover',
            'target_kb' => 500, 'max_upload_kb' => 8192,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [1920, 1280, 768, 480],
        ],
        'hero_mobile' => [
            'width' => 800, 'height' => 1000, 'fit' => 'cover',
            'target_kb' => 300, 'max_upload_kb' => 6144,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [800, 480],
        ],
        'banner' => [
            'width' => 1200, 'height' => 400, 'fit' => 'cover',
            'target_kb' => 300, 'max_upload_kb' => 6144,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [1200, 768, 480],
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
            'width' => 1600, 'height' => 500, 'fit' => 'cover',
            'target_kb' => 400, 'max_upload_kb' => 8192,
            'formats' => ['jpg', 'jpeg', 'png', 'webp'],
            'responsive' => [1600, 1200, 768],
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
