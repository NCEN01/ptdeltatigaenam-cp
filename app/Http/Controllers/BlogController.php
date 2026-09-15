<?php

namespace App\Http\Controllers;

use App\Helpers\HtmlSanitizer;
use App\Helpers\TableOfContents;
use App\Models\BlogCategory;
use App\Models\BlogPost;

class BlogController extends Controller
{
    /** Two rows of three cards per page. */
    private const PER_PAGE = 6;

    /** Slides in the "keep reading" rail on an article page. */
    private const KEEP_READING_LIMIT = 9;

    /**
     * Card listings never render the body, and `content` holds every locale's
     * full article — selecting it would ship megabytes the view throws away.
     */
    private const CARD_COLUMNS = [
        'id', 'slug', 'title', 'excerpt', 'featured_image',
        'location', 'published_at', 'blog_category_id',
    ];

    public function index()
    {
        $q = trim((string) request('q'));

        return view('pages.blog.index', [
            'posts' => BlogPost::published()->select(self::CARD_COLUMNS)->with('category')
                ->when($q !== '', function ($query) use ($q) {
                    // Escape SQL wildcards
                    $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);

                    // LOWER() di kedua sisi, bukan LIKE biasa. title dan excerpt
                    // adalah kolom terjemahan: di MariaDB tipe JSON sebenarnya
                    // LONGTEXT bercollation utf8mb4_bin, yang peka huruf besar-
                    // kecil. Akibatnya "supervisor" tidak menemukan apa pun
                    // sementara "Supervisor" menemukan artikelnya — pencarian
                    // terasa rusak bagi siapa pun yang mengetik huruf kecil.
                    // LOWER() dipilih daripada COLLATE karena tetap jalan di
                    // SQLite, yang dipakai test suite.
                    $needle = '%'.mb_strtolower($escaped).'%';

                    $query->where(function ($sub) use ($needle) {
                        $sub->whereRaw('LOWER(title) LIKE ?', [$needle])
                            ->orWhereRaw('LOWER(excerpt) LIKE ?', [$needle]);
                    });
                })
                ->latest('published_at')->paginate(self::PER_PAGE)->withQueryString()->fragment(self::RESULTS_ANCHOR),
            'categories' => BlogCategory::where('is_active', true)->orderBy('sort_order')->get(),
            'q' => $q,
        ]);
    }

    public function show(string $locale, string $slug)
    {
        $post = BlogPost::where('slug', $slug)->published()->with(['category', 'tags'])->firstOrFail();

        $sessionKey = 'viewed_post_' . $post->id;
        if (! session()->has($sessionKey)) {
            $post->increment('views');
            session()->put($sessionKey, true);
        }

        // Anchor the headings so the table of contents can scroll-spy them.
        $article = TableOfContents::build(HtmlSanitizer::clean($post->content));

        return view('pages.blog.show', [
            'post' => $post,
            'articleHtml' => $article['html'],
            'toc' => $article['items'],
            'latestPosts' => BlogPost::published()->where('id', '!=', $post->id)
                ->select(self::CARD_COLUMNS)->with('category')
                ->latest('published_at')->take(self::KEEP_READING_LIMIT)->get(),
        ]);
    }
}
