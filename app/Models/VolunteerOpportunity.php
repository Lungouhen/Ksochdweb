<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VolunteerOpportunity extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'requirements', 'event_id', 'location', 'starts_at', 'ends_at', 'spots_available', 'spots_filled', 'status', 'skills_required', 'metadata', 'created_by'];
    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'skills_required' => 'array', 'metadata' => 'array'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function applications(): HasMany { return $this->hasMany(VolunteerApplication::class); }
    public function hours(): HasMany { return $this->hasMany(VolunteerHour::class); }

    public function scopeActive($query) { return $query->where('status', 'active'); }
}
