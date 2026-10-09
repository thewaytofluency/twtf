<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Admin-controlled display order (the `sort_order` column) for landing page content.
 */
trait Sortable
{
    public static function nextSortOrder(): int
    {
        return (int) static::max('sort_order') + 1;
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Moves one step up or down. The whole list is renumbered 1..n first so rows that share a
     * sort_order (e.g. several created with the default) still swap predictably.
     */
    public function move(string $direction): void
    {
        DB::transaction(function () use ($direction) {
            $ids = array_map('intval', static::query()->ordered()->pluck('id')->all());
            $index = array_search((int) $this->getKey(), $ids, true);

            if ($index === false) {
                return;
            }

            $target = $direction === 'up' ? $index - 1 : $index + 1;

            if ($target < 0 || $target >= count($ids)) {
                return;
            }

            [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];

            foreach ($ids as $position => $id) {
                static::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });
    }
}
