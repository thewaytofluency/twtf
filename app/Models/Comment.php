<?php

namespace App\Models;

use App\Models\Concerns\Likeable;
use App\Observers\CommentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(CommentObserver::class)]
class Comment extends Model
{
    use HasFactory, Likeable;

    protected $fillable = ['blog_post_id', 'user_id', 'content'];

    protected $attributes = [
        'like_count' => 0,
    ];

    protected function casts(): array
    {
        return [
            'like_count' => 'integer',
        ];
    }

    public function blogPost(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
