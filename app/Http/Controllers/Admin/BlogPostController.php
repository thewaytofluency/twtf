<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    private const FILTERS = ['published', 'scheduled', 'draft'];

    public function index(Request $request): View
    {
        $filter = in_array($request->query('status'), self::FILTERS, true) ? $request->query('status') : null;

        $posts = BlogPost::with('author')
            ->when($filter === 'published', fn ($q) => $q->published())
            ->when($filter === 'scheduled', fn ($q) => $q->where('status', 'published')->where('published_at', '>', now()))
            ->when($filter === 'draft', fn ($q) => $q->where('status', 'draft'))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.blog-posts.index', [
            'blogPosts' => $posts,
            'filter' => $filter,
            'counts' => [
                'all' => BlogPost::count(),
                'published' => BlogPost::published()->count(),
                'scheduled' => BlogPost::where('status', 'published')->where('published_at', '>', now())->count(),
                'draft' => BlogPost::where('status', 'draft')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.blog-posts.create', [
            'blogPost' => new BlogPost(['status' => 'draft']),
        ]);
    }

    public function store(BlogPostRequest $request): RedirectResponse
    {
        $data = $this->postData($request);

        $post = BlogPost::create([
            ...$data,
            'slug' => $this->uniqueSlug(($data['slug'] ?? null) ?: $data['title']),
            'cover_image' => $request->hasFile('cover') ? $request->file('cover')->store('blog/covers', 'public') : null,
            'author_id' => Auth::id(),
        ]);

        return redirect()->route('admin.blog-posts.edit', $post)->with('status', $this->savedMessage($post, 'created'));
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.edit', [
            'blogPost' => $blogPost,
        ]);
    }

    public function update(BlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $this->postData($request, $blogPost);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? null) ?: $data['title'], $blogPost);

        if ($request->hasFile('cover')) {
            $this->deleteCover($blogPost);
            $data['cover_image'] = $request->file('cover')->store('blog/covers', 'public');
        } elseif ($request->boolean('remove_cover')) {
            $this->deleteCover($blogPost);
            $data['cover_image'] = null;
        }

        $blogPost->update($data);

        return redirect()->route('admin.blog-posts.edit', $blogPost)->with('status', $this->savedMessage($blogPost, 'updated'));
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $this->deleteCover($blogPost);
        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')->with('status', 'Post deleted.');
    }

    /** Inline images from the editor (toolbar button, paste or drag-and-drop). Returns the public URL. */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $path = $request->file('image')->store('blog/images', 'public');

        return response()->json(['url' => '/storage/'.$path]);
    }

    /** Fields shared by store and update, with the publish state resolved. */
    private function postData(BlogPostRequest $request, ?BlogPost $existing = null): array
    {
        $data = $request->safe()->only(['title', 'slug', 'excerpt', 'content', 'status']);
        $data['slug'] = $data['slug'] ?? null;
        $data['excerpt'] = filled($data['excerpt'] ?? null) ? $data['excerpt'] : null;

        if ($data['status'] === 'draft') {
            // Keep a chosen schedule date on a draft, if there is one.
            $data['published_at'] = $request->date('published_at');
        } else {
            // Publishing: an explicit date wins (future = scheduled), then the existing date, else now.
            $data['published_at'] = $request->date('published_at') ?? $existing?->published_at ?? now();
        }

        return $data;
    }

    private function savedMessage(BlogPost $post, string $verb): string
    {
        return match (true) {
            $post->status === 'draft' => "Draft {$verb}.",
            $post->isScheduled() => "Post {$verb} and scheduled for {$post->published_at->format('M j, Y H:i')}.",
            default => "Post {$verb} and published.",
        };
    }

    private function deleteCover(BlogPost $post): void
    {
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }
    }

    private function uniqueSlug(string $source, ?BlogPost $ignoring = null): string
    {
        $base = Str::slug($source) ?: 'post';
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
