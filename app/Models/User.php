<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\HasRoles;
use App\Models\Traits\Auditable;
use App\Models\Traits\HasMedia;
use App\Models\Traits\HasComments;
use App\Models\Traits\HasActivityLog;
use App\Models\Traits\SoftDeletesWithAuthor;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, Auditable, HasMedia, HasComments, HasActivityLog, SoftDeletesWithAuthor;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'bio',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function activeMembership()
    {
        return $this->hasOne(Membership::class)->where('status', 'active');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_registrations');
    }

    public function volunteerApplications()
    {
        return $this->hasMany(VolunteerApplication::class);
    }

    public function volunteerHours()
    {
        return $this->hasMany(VolunteerHour::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function createdPages()
    {
        return $this->hasMany(Page::class, 'created_by');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'created_by');
    }

    public function assignedMessages()
    {
        return $this->hasMany(ContactMessage::class, 'assigned_to');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['super_admin', 'admin', 'staff']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function hasActiveMembership(): bool
    {
        return $this->activeMembership()->exists();
    }

    public function getTotalVolunteerHoursAttribute()
    {
        return $this->volunteerHours()->where('is_approved', true)->sum('hours');
    }
}
