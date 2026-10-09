<?php

namespace App\Models;

use App\Models\Concerns\Likeable;
use App\Support\PostHtml;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[RouteKey('slug')]
class BlogPost extends Model
{
    use HasFactory, Likeable;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'cover_image', 'content', 'status', 'published_at', 'author_id',
    ];

    protected $attributes = [
        'like_count' => 0,
        'comment_count' => 0,
    ];

    protected function casts(): array
    {
        return [
            'like_count' => 'integer',
            'comment_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // A post created as published without an explicit date goes live right now.
        static::creating(function (BlogPost $post) {
            if (($post->status ?? 'published') === 'published' && $post->published_at === null) {
                $post->published_at = now();
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** Always stored sanitized - see PostHtml. Plain text from older posts/seeders is wrapped in paragraphs. */
    protected function content(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => PostHtml::clean($value));
    }

    protected function readTimeMinutes(): Attribute
    {
        return Attribute::make(
            get: fn () => max(1, (int) ceil(str_word_count(PostHtml::toText($this->content)) / 200)),
        );
    }

    /** The hand-written excerpt if there is one, otherwise the start of the article. */
    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => filled($value) ? $value : Str::limit(PostHtml::toText($this->content), 160),
        );
    }

    protected function coverUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cover_image ? '/storage/'.$this->cover_image : null,
        );
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function isScheduled(): bool
    {
        return $this->status === 'published' && $this->published_at?->isFuture();
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', 'published')->where('published_at', '<=', now());
    }
}
