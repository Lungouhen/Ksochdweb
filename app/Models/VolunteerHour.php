<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerHour extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'opportunity_id', 'event_id', 'date', 'hours', 'description', 'approved_by', 'is_approved', 'approved_at'];
    protected $casts = ['date' => 'date', 'approved_at' => 'datetime', 'is_approved' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function opportunity(): BelongsTo { return $this->belongsTo(VolunteerOpportunity::class); }
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }

    public function approve(?int $approvedBy = null): void { $this->update(['is_approved' => true, 'approved_by' => $approvedBy ?? auth()->id(), 'approved_at' => now()]); }
}
