<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_system',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function givePermission(array|string $permissions): self
    {
        $current = $this->permissions ?? [];
        $new = array_merge($current, is_array($permissions) ? $permissions : [$permissions]);
        $this->update(['permissions' => array_unique($new)]);
        return $this;
    }

    public function removePermission(array|string $permissions): self
    {
        $current = $this->permissions ?? [];
        $toRemove = is_array($permissions) ? $permissions : [$permissions];
        $this->update(['permissions' => array_diff($current, $toRemove)]);
        return $this;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }
}
