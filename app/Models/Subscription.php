<?php

namespace App\Models;

use App\Enums\SubscriptionMethod;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'plan_id', 'method', 'payment_channel', 'status', 'amount',
        'proof_path', 'gateway_reference', 'starts_at', 'ends_at',
        'reviewed_by', 'reviewed_at', 'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'method' => SubscriptionMethod::class,
            'status' => SubscriptionStatus::class,
            'amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isCurrentlyActive(): bool
    {
        return $this->status === SubscriptionStatus::Active
            && $this->ends_at !== null
            && $this->ends_at->isFuture();
    }

    public function approve(User $reviewer, ?int $periodDays = 30): void
    {
        $startsAt = Carbon::now();

        $this->update([
            'status' => SubscriptionStatus::Active,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->clone()->addDays($periodDays),
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => Carbon::now(),
        ]);
    }

    public function reject(User $reviewer, ?string $notes = null): void
    {
        $this->update([
            'status' => SubscriptionStatus::Rejected,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => Carbon::now(),
            'admin_notes' => $notes,
        ]);
    }
}
