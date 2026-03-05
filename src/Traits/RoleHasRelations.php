<?php

namespace Endone777\Roles\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait RoleHasRelations
{
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(config('roles.models.permission'))->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('auth.providers.users.model'))->withTimestamps();
    }

    public function attachPermission(int|object $permission): int|bool
    {
        return (!$this->permissions()->get()->contains($permission)) ? $this->permissions()->attach($permission) : true;
    }

    public function detachPermission(int|object $permission): int
    {
        return $this->permissions()->detach($permission);
    }

    public function detachAllPermissions(): int
    {
        return $this->permissions()->detach();
    }

    public function syncPermissions(array|Collection $permissions): array
    {
        return $this->permissions()->sync($permissions);
    }
}
