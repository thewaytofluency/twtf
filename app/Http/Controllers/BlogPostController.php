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
            'readIds' => Auth::user()->progressFor('blog_post')->keys(),
        ]);
    }

    public function show(BlogPost $blogPost): View
    {
        // Drafts and scheduled posts are 404 for students; admins can open them to preview.
        abort_unless($blogPost->isPublished() || Auth::user()?->isAdmin(), 404);

        $user = Auth::user();

        // Only published posts take part in the reading flow (a draft preview doesn't track
        // reading or link into the live sequence).
        $live = $blogPost->isPublished();
        if ($live) {
            $user->markViewed($blogPost);
        }

        $published = BlogPost::published();

        return view('blog.show', [
            'blogPost' => $blogPost->load(['author', 'comments' => fn ($query) => $query->with('user')->oldest()]),
            // Older / newer neighbours by publish date: reading order for a blog is time, not a path.
            'olderPost' => $live
                ? (clone $published)->where('published_at', '<', $blogPost->published_at)->latest('published_at')->first()
                : null,
            'newerPost' => $live
                ? (clone $published)->where('published_at', '>', $blogPost->published_at)->oldest('published_at')->first()
                : null,
            'morePosts' => (clone $published)->whereKeyNot($blogPost->getKey())->latest('published_at')->limit(3)->get(),
            'readIds' => $user->progressFor('blog_post')->keys(),
        ]);
    }
}
