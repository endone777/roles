<?php

namespace Endone777\Roles\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait PermissionHasRelations
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('roles.models.role'))->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('auth.providers.users.model'))->withTimestamps();
    }
}
