<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['content', 'parent_id', 'user_id', 'guest_name', 'guest_email', 'is_approved', 'approved_at', 'approved_by', 'ip_address', 'user_agent', 'edited_by', 'edited_at'];
    protected $casts = ['is_approved' => 'boolean', 'approved_at' => 'datetime', 'edited_at' => 'datetime'];

    public function commentable(): MorphTo { return $this->morphTo(); }
    public function parent(): BelongsTo { return $this->belongsTo(Comment::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(Comment::class, 'parent_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function editor(): BelongsTo { return $this->belongsTo(User::class, 'edited_by'); }

    public function approve(?int $approvedBy = null): void { $this->update(['is_approved' => true, 'approved_by' => $approvedBy ?? auth()->id(), 'approved_at' => now()]); }
}
