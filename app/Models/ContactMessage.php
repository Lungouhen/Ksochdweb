<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'subject', 'message', 'phone', 'status', 'assigned_to', 'internal_notes', 'read_at', 'replied_at', 'ip_address'];
    protected $casts = ['read_at' => 'datetime', 'replied_at' => 'datetime'];

    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }

    public function markAsRead(): void { $this->update(['status' => 'read', 'read_at' => now()]); }
    public function markAsReplied(): void { $this->update(['status' => 'replied', 'replied_at' => now()]); }
    public function archive(): void { $this->update(['status' => 'archived']); }
}
