<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'title', 'content', 'template_id', 'status', 'scheduled_at', 'sent_at', 'recipient_count', 'opened_count', 'clicked_count', 'created_by', 'sent_by'];
    protected $casts = ['scheduled_at' => 'datetime', 'sent_at' => 'datetime'];

    public function template(): BelongsTo { return $this->belongsTo(Page::class, 'template_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function sender(): BelongsTo { return $this->belongsTo(User::class, 'sent_by'); }
    public function subscribers(): BelongsToMany { return $this->belongsToMany(Subscriber::class, 'newsletter_subscriber')->withPivot(['opened', 'opened_at', 'clicked', 'clicked_at'])->withTimestamps(); }
}
