<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }

        return false;
    }

    public function assignRole(string $roleSlug): self
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        
        if (!$this->hasRole($roleSlug)) {
            $this->roles()->attach($role);
        }

        return $this;
    }

    public function removeRole(string $roleSlug): self
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        $this->roles()->detach($role);

        return $this;
    }

    public function syncRoles(array $roleSlugs): self
    {
        $roles = Role::whereIn('slug', $roleSlugs)->get();
        $this->roles()->sync($roles);

        return $this;
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function hasAllRoles(array $roles): bool
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }
}
