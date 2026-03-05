<?php

namespace Endone777\Roles\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface RoleHasRelations
{
    public function permissions(): BelongsToMany;

    public function users(): BelongsToMany;

    public function attachPermission(int|object $permission): int|bool;

    public function detachPermission(int|object $permission): int;

    public function detachAllPermissions(): int;

    public function syncPermissions(array|Collection $permissions): array;
}
