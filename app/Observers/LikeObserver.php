<?php

namespace App\Observers;

use App\Models\Like;

class LikeObserver
{
    public function created(Like $like): void
    {
        $like->likeable?->increment('like_count');
    }

    public function deleted(Like $like): void
    {
        $like->likeable?->decrement('like_count');
    }
}
