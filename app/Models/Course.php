<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory, Sortable;

    /**
     * Accent colour choices for the badge gradient. Full class names (and app/Models in
     * tailwind.config.js `content`) so Tailwind generates them.
     */
    public const ACCENTS = [
        'green' => 'from-green-400 to-green-600',
        'blue' => 'from-blue-400 to-blue-600',
        'purple' => 'from-purple-400 to-purple-600',
        'amber' => 'from-amber-400 to-amber-600',
        'rose' => 'from-rose-400 to-rose-600',
        'teal' => 'from-teal-400 to-teal-600',
    ];

    protected $fillable = [
        'title', 'description', 'badge_type', 'emoji', 'accent', 'image', 'cta_url', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected function accentClasses(): Attribute
    {
        return Attribute::make(
            get: fn () => self::ACCENTS[$this->accent] ?? self::ACCENTS['blue'],
        );
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
