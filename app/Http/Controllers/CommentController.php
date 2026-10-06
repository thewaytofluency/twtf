<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, BlogPost $blogPost): RedirectResponse
    {
        abort_unless($blogPost->isPublished(), 404);

        $blogPost->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->validated('content'),
        ]);

        return redirect()->route('blog.show', $blogPost)->with('status', 'Comment posted.');
    }
}
