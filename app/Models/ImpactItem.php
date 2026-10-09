<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpactItem extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'title', 'description', 'badge_type', 'stat', 'emoji', 'image', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** The image to show as the badge: only when the admin chose "image" and one is uploaded. */
    protected function badgeImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->badge_type === 'image' && $this->image ? '/storage/'.$this->image : null,
        );
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image ? '/storage/'.$this->image : null,
        );
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
