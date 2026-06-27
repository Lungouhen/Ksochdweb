<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_type_id',
        'status',
        'reason',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'answers',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'answers' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approve(?int $reviewedBy = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewedBy ?? auth()->id(),
            'reviewed_at' => now(),
        ]);
    }

    public function reject(string $reason, ?int $reviewedBy = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewedBy ?? auth()->id(),
            'reviewed_at' => now(),
        ]);
    }
}
