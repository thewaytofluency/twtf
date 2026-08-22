<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Observers\LikeObserver;

#[ObservedBy(LikeObserver::class)]
class Like extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['user_id', 'likeable_type', 'likeable_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}
