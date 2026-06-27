<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'user_id', 'email', 'name', 'status', 'guests', 'amount_paid', 'notes', 'answers', 'confirmed_at', 'cancelled_at'];
    protected $casts = ['answers' => 'array', 'confirmed_at' => 'datetime', 'cancelled_at' => 'datetime', 'amount_paid' => 'decimal:2'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    
    public function confirm(): void { $this->update(['status' => 'confirmed', 'confirmed_at' => now()]); }
    public function cancel(): void { $this->update(['status' => 'cancelled', 'cancelled_at' => now()]); }
}
