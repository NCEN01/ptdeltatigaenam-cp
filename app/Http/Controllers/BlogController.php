<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $q = trim((string) request('q'));

        return view('pages.blog.index', [
            'posts' => BlogPost::published()->with('category')
                ->when($q !== '', function ($query) use ($q) {
                    // title/excerpt are translatable JSON columns — a LIKE matches the
                    // keyword in any stored locale.
                    $query->where(function ($sub) use ($q) {
                        $sub->where('title', 'like', "%{$q}%")
                            ->orWhere('excerpt', 'like', "%{$q}%");
                    });
                })
                ->latest('published_at')->paginate(9)->withQueryString(),
            'categories' => BlogCategory::where('is_active', true)->orderBy('sort_order')->get(),
            'q' => $q,
        ]);
    }

    public function show(string $locale, string $slug)
    {
        $post = BlogPost::where('slug', $slug)->published()->with(['category', 'author', 'tags'])->firstOrFail();
        $post->increment('views');

        return view('pages.blog.show', [
            'post' => $post,
            'related' => BlogPost::published()->where('id', '!=', $post->id)
                ->where('blog_category_id', $post->blog_category_id)->latest('published_at')->take(3)->get(),
        ]);
    }
}
