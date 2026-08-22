<?php

namespace App\Observers;

use App\Models\Comment;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        $comment->blogPost?->increment('comment_count');
    }

    public function deleted(Comment $comment): void
    {
        $comment->blogPost?->decrement('comment_count');
    }
}
