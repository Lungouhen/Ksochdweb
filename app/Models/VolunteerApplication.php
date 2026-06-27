<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerApplication extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'opportunity_id', 'status', 'message', 'rejection_reason', 'hours_worked', 'feedback', 'reviewed_by', 'reviewed_at'];
    protected $casts = ['reviewed_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function opportunity(): BelongsTo { return $this->belongsTo(VolunteerOpportunity::class, 'opportunity_id'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function approve(?int $reviewedBy = null): void { $this->update(['status' => 'approved', 'reviewed_by' => $reviewedBy ?? auth()->id(), 'reviewed_at' => now()]); }
    public function reject(string $reason, ?int $reviewedBy = null): void { $this->update(['status' => 'rejected', 'rejection_reason' => $reason, 'reviewed_by' => $reviewedBy ?? auth()->id(), 'reviewed_at' => now()]); }
}
