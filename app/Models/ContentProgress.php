<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * One row per (student, video|doc|blog post): when they last opened it and, for lessons,
 * when they marked it complete. Drives the "continue where you left off" flow, the
 * completed ticks in the lists and the profile statistics.
 */
class ContentProgress extends Model
{
    protected $table = 'content_progress';

    protected $fillable = ['user_id', 'progressable_type', 'progressable_id', 'viewed_at', 'completed_at'];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function progressable(): MorphTo
    {
        return $this->morphTo();
    }
}
