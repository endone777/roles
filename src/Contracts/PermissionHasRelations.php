<?php

namespace Endone777\Roles\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface PermissionHasRelations
{
    public function roles(): BelongsToMany;

    public function users(): BelongsToMany;
}
