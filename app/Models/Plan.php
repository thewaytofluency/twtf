<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'fee', 'access_level', 'description', 'features', 'is_popular', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'access_level' => 'integer',
            'features' => 'array',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /** "1500 MZN" / "1500.50 MZN", or "Free". Whole amounts drop the decimals. */
    public function formattedFee(): string
    {
        if ((float) $this->fee == 0.0) {
            return 'Free';
        }

        $decimals = (float) $this->fee == floor((float) $this->fee) ? 0 : 2;

        return number_format((float) $this->fee, $decimals, '.', '').' MZN';
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
