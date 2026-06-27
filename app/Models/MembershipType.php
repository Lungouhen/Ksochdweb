<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\MembershipStatus;

class MembershipType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'duration_days',
        'benefits',
        'permissions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'benefits' => 'array',
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(MembershipApplication::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->billing_cycle === 'lifetime') {
            return 'Lifetime';
        }

        return match($this->billing_cycle) {
            'monthly' => 'Per Month',
            'yearly' => 'Per Year',
            default => $this->duration_days . ' days',
        };
    }
}
