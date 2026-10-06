<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        return view('blog.index', [
            'blogPosts' => BlogPost::with('author')->latest()->paginate(9),
        ]);
    }

    public function show(BlogPost $blogPost): View
    {
        return view('blog.show', [
            'blogPost' => $blogPost->load(['author', 'comments' => fn ($query) => $query->with('user')->oldest()]),
        ]);
    }
}
