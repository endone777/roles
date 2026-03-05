<?php

namespace Endone777\Roles\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface HasRoleAndPermission
{
    public function roles(): BelongsToMany;

    public function getRoles(): Collection;

    public function hasRole(int|string|array $role, bool $all = false): bool;

    public function hasOneRole(int|string|array $role): bool;

    public function hasAllRoles(int|string|array $role): bool;

    public function checkRole(int|string $role): bool;

    public function attachRole(int|Model $role): null|bool;

    public function detachRole(int|Model $role): int;

    public function detachAllRoles(): int;

    public function syncRoles(array|Collection $roles): array;

    public function level(): int;

    public function rolePermissions(): Builder;

    public function userPermissions(): BelongsToMany;

    public function getPermissions(): Collection;

    public function hasPermission(int|string|array $permission, bool $all = false): bool;

    public function hasOnePermission(int|string|array $permission): bool;

    public function hasAllPermissions(int|string|array $permission): bool;

    public function checkPermission(int|string $permission): bool;

    public function allowed(string $providedPermission, Model $entity, bool $owner = true, string $ownerColumn = 'user_id'): bool;

    public function attachPermission(int|Model $permission): null|bool;

    public function detachPermission(int|Model $permission): int;

    public function detachAllPermissions(): int;

    public function syncPermissions(array|Collection $permissions): array;
}
