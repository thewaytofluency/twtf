<?php

namespace App\Models;

use App\Models\Concerns\Likeable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
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

    protected $fillable = ['title', 'slug', 'content', 'author_id'];

    protected $attributes = [
        'like_count' => 0,
        'comment_count' => 0,
    ];

    protected function casts(): array
    {
        return [
            'like_count' => 'integer',
            'comment_count' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    protected function readTimeMinutes(): Attribute
    {
        return Attribute::make(
            get: fn () => max(1, (int) ceil(str_word_count(strip_tags($this->content)) / 200)),
        );
    }

    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::limit(strip_tags($this->content), 160),
        );
    }
}
