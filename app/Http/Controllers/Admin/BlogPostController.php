<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        return view('admin.blog-posts.index', [
            'blogPosts' => BlogPost::with('author')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.blog-posts.create', [
            'blogPost' => new BlogPost,
        ]);
    }

    public function store(BlogPostRequest $request): RedirectResponse
    {
        $data = $request->validated();

        BlogPost::create([
            ...$data,
            'slug' => $this->uniqueSlug($data['title']),
            'author_id' => Auth::id(),
        ]);

        return redirect()->route('admin.blog-posts.index')->with('status', 'Post created.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.edit', [
            'blogPost' => $blogPost,
        ]);
    }

    public function update(BlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $request->validated();

        if ($blogPost->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $blogPost);
        }

        $blogPost->update($data);

        return redirect()->route('admin.blog-posts.index')->with('status', 'Post updated.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')->with('status', 'Post deleted.');
    }

    private function uniqueSlug(string $title, ?BlogPost $ignoring = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 2;

        while (
            BlogPost::where('slug', $slug)
                ->when($ignoring, fn ($query) => $query->whereKeyNot($ignoring->id))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
