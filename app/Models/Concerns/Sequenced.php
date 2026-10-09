<?php

namespace App\Models\Concerns;

use App\Models\ContentProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Lessons (videos, documents) live in one ordered path: beginner -> intermediate -> advanced
 * (documents without a level come last), and within a level by the admin's `sort_order`.
 * This is what powers "previous / next" and the playlist sidebar, so a student never has to go
 * back to the list to find what comes next.
 */
trait Sequenced
{
    public function progress(): MorphMany
    {
        return $this->morphMany(ContentProgress::class, 'progressable');
    }

    /** Scope: path order (level, then the admin's sort_order, then id). Use as Model::inSequence(). */
    #[Scope]
    protected function inSequence(Builder $query): void
    {
        $query
            ->orderByRaw("case course_level when 'beginner' then 1 when 'intermediate' then 2 when 'advanced' then 3 else 4 end")
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * The lesson right before and right after this one in the whole path, regardless of level
     * (the last beginner lesson leads on to the first intermediate one).
     *
     * @return array{previous: static|null, next: static|null}
     */
    public function neighbours(): array
    {
        $path = static::query()->inSequence()->get();
        $index = $path->search(fn ($lesson) => $lesson->is($this));

        return [
            'previous' => $index > 0 ? $path[$index - 1] : null,
            'next' => $index !== false ? $path->get($index + 1) : null,
        ];
    }

    /** The lessons of the same level, in order — the playlist shown beside the current lesson. */
    public function playlist(): Collection
    {
        return static::query()->inSequence()
            ->when(
                $this->course_level,
                fn (Builder $query) => $query->where('course_level', $this->course_level),
                fn (Builder $query) => $query->whereNull('course_level'),
            )
            ->get();
    }

    public function isCompletedBy(?User $user): bool
    {
        return $user !== null
            && $this->progress()->where('user_id', $user->id)->whereNotNull('completed_at')->exists();
    }

    public static function nextSortOrder(): int
    {
        return (int) static::max('sort_order') + 1;
    }
}
