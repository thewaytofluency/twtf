<?php

namespace App\Models;

use App\Enums\CourseLevel;
use App\Models\Concerns\Likeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory, Likeable;

    protected $fillable = [
        'title', 'description', 'youtube_url', 'course_level',
        'required_access_level', 'created_by',
    ];

    protected $attributes = [
        'like_count' => 0,
    ];

    protected function casts(): array
    {
        return [
            'course_level' => CourseLevel::class,
            'required_access_level' => 'integer',
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
}
