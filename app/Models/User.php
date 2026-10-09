<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'photo', 'contact'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Matches the DB-level defaults so a freshly-instantiated (not yet refreshed
     * from the DB) model reflects them in memory too - e.g. Laravel's test
     * `actingAs()` reuses the exact object from `factory()->create()` across
     * requests within a test, without a DB round-trip in between.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'student',
        'status' => 'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function contentProgress(): HasMany
    {
        return $this->hasMany(ContentProgress::class);
    }

    /** Record that the student opened a video / document / post (keeps the first-view row, bumps viewed_at). */
    public function markViewed(Model $item): ContentProgress
    {
        return $this->contentProgress()->updateOrCreate(
            ['progressable_type' => $item->getMorphClass(), 'progressable_id' => $item->getKey()],
            ['viewed_at' => now()],
        );
    }

    public function setCompleted(Model $item, bool $completed): ContentProgress
    {
        return $this->contentProgress()->updateOrCreate(
            ['progressable_type' => $item->getMorphClass(), 'progressable_id' => $item->getKey()],
            ['viewed_at' => now(), 'completed_at' => $completed ? now() : null],
        );
    }

    /** Progress rows for one kind of content ('video', 'doc', 'blog_post'), keyed by item id. */
    public function progressFor(string $morphAlias): \Illuminate\Support\Collection
    {
        return $this->contentProgress()
            ->where('progressable_type', $morphAlias)
            ->get()
            ->keyBy('progressable_id');
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::make(get: fn () => $this->photo ? '/storage/'.$this->photo : null);
    }

    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn () => collect(explode(' ', trim($this->name)))->filter()->take(2)->map(fn ($n) => mb_strtoupper(mb_substr($n, 0, 1)))->implode('')
        );
    }

    public function currentSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->latestOfMany('ends_at');
    }

    public function pendingSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'pending')
            ->latestOfMany();
    }

    public function currentAccessLevel(): int
    {
        return $this->currentSubscription?->plan?->access_level ?? 0;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }
}
