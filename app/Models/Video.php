<?php

namespace App\Models;

use App\Enums\CourseLevel;
use App\Models\Concerns\Likeable;
use App\Models\Concerns\Sequenced;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory, Likeable, Sequenced;

    protected $fillable = [
        'title', 'description', 'youtube_url', 'course_level',
        'required_access_level', 'sort_order', 'created_by',
    ];

    protected $attributes = [
        'like_count' => 0,
    ];

    protected function casts(): array
    {
        return [
            'course_level' => CourseLevel::class,
            'required_access_level' => 'integer',
            'sort_order' => 'integer',
            'like_count' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isAccessibleBy(?User $user): bool
    {
        $level = $user?->currentAccessLevel() ?? 0;

        return $level >= $this->required_access_level;
    }

    /**
     * Uses parse_url()/parse_str() rather than a single regex since
     * VideoRequest's validation only checks the domain substring
     * (youtube.com/youtu.be), so a saved URL like youtube.com/watch?list=X&v=Y
     * (a real shape YouTube produces from "copy link" on a playlist) must
     * still resolve correctly regardless of query-param order. Returns null
     * if extraction fails; the show view falls back to an external link.
     */
    protected function embedUrl(): Attribute
    {
        return Attribute::make(get: function (): ?string {
            $parts = parse_url($this->youtube_url ?? '');
            $host = $parts['host'] ?? '';
            $path = $parts['path'] ?? '';
            parse_str($parts['query'] ?? '', $query);

            $id = match (true) {
                str_contains($host, 'youtu.be') => ltrim($path, '/'),
                str_contains($host, 'youtube.com') && isset($query['v']) => $query['v'],
                str_contains($host, 'youtube.com') && str_starts_with($path, '/embed/') => substr($path, 7),
                default => null,
            };

            return preg_match('/^[A-Za-z0-9_-]{11}$/', (string) $id)
                ? "https://www.youtube.com/embed/{$id}"
                : null;
        });
    }
}
