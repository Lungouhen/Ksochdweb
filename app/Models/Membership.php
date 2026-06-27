<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\MembershipStatus;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_type_id',
        'status',
        'starts_at',
        'expires_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', MembershipStatus::ACTIVE->value)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('status', MembershipStatus::EXPIRED->value)
            ->orWhere(function ($q) {
                $q->where('status', MembershipStatus::ACTIVE->value)
                  ->where('expires_at', '<', now());
            });
    }

    public function isActive(): bool
    {
        return $this->status === MembershipStatus::ACTIVE->value &&
            ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return $this->expires_at->diffInDays(now()) <= $days;
    }
}
