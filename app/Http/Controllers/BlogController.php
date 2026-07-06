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
                    // Escape SQL wildcards
                    $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
                    $query->where(function ($sub) use ($escaped) {
                        $sub->where('title', 'like', "%{$escaped}%")
                            ->orWhere('excerpt', 'like', "%{$escaped}%");
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
        
        $sessionKey = 'viewed_post_' . $post->id;
        if (! session()->has($sessionKey)) {
            $post->increment('views');
            session()->put($sessionKey, true);
        }

        return view('pages.blog.show', [
            'post' => $post,
            'related' => BlogPost::published()->where('id', '!=', $post->id)
                ->where('blog_category_id', $post->blog_category_id)->latest('published_at')->take(3)->get(),
        ]);
    }
}
