<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        return view('blog.index', [
            'blogPosts' => BlogPost::with('author')->published()->latest('published_at')->paginate(9),
        ]);
    }

    public function show(BlogPost $blogPost): View
    {
        // Drafts and scheduled posts are 404 for students; admins can open them to preview.
        abort_unless($blogPost->isPublished() || Auth::user()?->isAdmin(), 404);

        return view('blog.show', [
            'blogPost' => $blogPost->load(['author', 'comments' => fn ($query) => $query->with('user')->oldest()]),
        ]);
    }
}
