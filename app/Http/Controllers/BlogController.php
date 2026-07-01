<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        return view('pages.blog.index', [
            'posts' => BlogPost::published()->with('category')->latest('published_at')->paginate(9),
            'categories' => BlogCategory::where('is_active', true)->orderBy('sort_order')->get(),
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
