<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'name', 'is_active', 'confirmed_at', 'preferences', 'subscribed_via'];
    protected $casts = ['is_active' => 'boolean', 'confirmed_at' => 'datetime', 'preferences' => 'array'];

    public function referrer(): BelongsTo { return $this->belongsTo(User::class, 'subscribed_via'); }
    public function newsletters(): BelongsToMany { return $this->belongsToMany(Newsletter::class, 'newsletter_subscriber')->withPivot(['opened', 'opened_at', 'clicked', 'clicked_at'])->withTimestamps(); }
}
