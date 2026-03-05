<?php

namespace Endone777\Roles\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use InvalidArgumentException;

trait HasRoleAndPermission
{
    protected ?Collection $cachedRoles = null;

    protected ?Collection $cachedPermissions = null;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('roles.models.role'))->withTimestamps();
    }

    public function getRoles(): Collection
    {
        return $this->cachedRoles ??= (
            $this->relationLoaded('roles')
                ? $this->getRelation('roles')
                : $this->roles()->get()
        );
    }

    public function hasRole(int|string|array $role, bool $all = false): bool
    {
        if ($this->isPretendEnabled()) {
            return $this->pretend('hasRole');
        }

        if (!$all) {
            return $this->hasOneRole($role);
        }

        return $this->hasAllRoles($role);
    }

    public function hasOneRole(int|string|array $role): bool
    {
        foreach ($this->getArrayFrom($role) as $role) {
            if ($this->checkRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllRoles(int|string|array $role): bool
    {
        foreach ($this->getArrayFrom($role) as $role) {
            if (!$this->checkRole($role)) {
                return false;
            }
        }

        return true;
    }

    public function checkRole(int|string $role): bool
    {
        return $this->getRoles()->contains(function ($value) use ($role) {
            return $role == $value->id || Str::is($role, $value->slug);
        });
    }

    /**
     * Scope to eager load roles and direct user permissions.
     *
     * Usage: User::withRolesAndPermissions()->get()
     * This prevents N+1 on getRoles() and partially on getPermissions().
     * Note: rolePermissions() (permissions inherited via roles) still executes per-user.
     */
    public function scopeWithRolesAndPermissions(Builder $query): Builder
    {
        return $query->with(['roles', 'userPermissions']);
    }

    public function attachRole(int|Model $role): null|bool
    {
        if ($this->getRoles()->contains($role)) {
            return true;
        }
        $this->cachedRoles = null;
        return $this->roles()->attach($role);
    }

    public function detachRole(int|Model|null $role): int
    {
        $this->cachedRoles = null;

        return $this->roles()->detach($role);
    }

    public function detachAllRoles(): int
    {
        $this->cachedRoles = null;

        return $this->roles()->detach();
    }

    public function syncRoles(array|Collection $roles): array
    {
        $this->cachedRoles = null;

        return $this->roles()->sync($roles);
    }

    public function level(): int
    {
        return ($role = $this->getRoles()->sortByDesc('level')->first()) ? $role->level : 0;
    }

    public function rolePermissions(): Builder
    {
        $permissionModel = app(config('roles.models.permission'));

        if (!$permissionModel instanceof Model) {
            throw new InvalidArgumentException('[roles.models.permission] must be an instance of \Illuminate\Database\Eloquent\Model');
        }

        return $permissionModel
            ::select(['permissions.*', 'permission_role.created_at as pivot_created_at', 'permission_role.updated_at as pivot_updated_at'])
            ->join('permission_role', 'permission_role.permission_id', '=', 'permissions.id')
            ->join('roles', 'roles.id', '=', 'permission_role.role_id')
            ->whereIn('roles.id', $this->getRoles()->pluck('id')->toArray())
            ->orWhere('roles.level', '<', $this->level())
            ->groupBy(['permissions.id', 'permissions.name', 'permissions.slug', 'permissions.description', 'permissions.model', 'permissions.created_at', 'permissions.updated_at', 'permission_role.created_at', 'permission_role.updated_at']);
    }

    public function userPermissions(): BelongsToMany
    {
        return $this->belongsToMany(config('roles.models.permission'))->withTimestamps();
    }

    public function getPermissions(): Collection
    {
        if ($this->cachedPermissions !== null) {
            return $this->cachedPermissions;
        }

        $userPermissions = $this->relationLoaded('userPermissions')
            ? $this->getRelation('userPermissions')
            : $this->userPermissions()->get();

        return $this->cachedPermissions = $this->rolePermissions()->get()->merge($userPermissions);
    }

    public function hasPermission(int|string|array $permission, bool $all = false): bool
    {
        if ($this->isPretendEnabled()) {
            return $this->pretend('hasPermission');
        }

        if (!$all) {
            return $this->hasOnePermission($permission);
        }

        return $this->hasAllPermissions($permission);
    }

    public function hasOnePermission(int|string|array $permission): bool
    {
        foreach ($this->getArrayFrom($permission) as $permission) {
            if ($this->checkPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllPermissions(int|string|array $permission): bool
    {
        foreach ($this->getArrayFrom($permission) as $permission) {
            if (!$this->checkPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    public function checkPermission(int|string $permission): bool
    {
        return $this->getPermissions()->contains(function ($value) use ($permission) {
            return $permission == $value->id || Str::is($permission, $value->slug);
        });
    }

    public function allowed(string $providedPermission, Model $entity, bool $owner = true, string $ownerColumn = 'user_id'): bool
    {
        if ($this->isPretendEnabled()) {
            return $this->pretend('allowed');
        }

        if ($owner === true && $entity->{$ownerColumn} == $this->id) {
            return true;
        }

        return $this->isAllowed($providedPermission, $entity);
    }

    protected function isAllowed(string $providedPermission, Model $entity): bool
    {
        foreach ($this->getPermissions() as $permission) {
            if ($permission->model != '' && get_class($entity) == $permission->model
                && ($permission->id == $providedPermission || $permission->slug === $providedPermission)
            ) {
                return true;
            }
        }

        return false;
    }

    public function attachPermission(int|Model $permission): null|bool
    {
        if ($this->getPermissions()->contains($permission)) {
            return true;
        }
        $this->cachedPermissions = null;
        return $this->userPermissions()->attach($permission);
    }

    public function detachPermission(int|Model $permission): int
    {
        $this->cachedPermissions = null;

        return $this->userPermissions()->detach($permission);
    }

    public function detachAllPermissions(): int
    {
        $this->cachedPermissions = null;

        return $this->userPermissions()->detach();
    }

    public function syncPermissions(array|Collection $permissions): array
    {
        $this->cachedPermissions = null;

        return $this->userPermissions()->sync($permissions);
    }

    private function isPretendEnabled(): bool
    {
        return (bool) config('roles.pretend.enabled');
    }

    private function pretend(string $option): bool
    {
        return (bool) config('roles.pretend.options.' . $option);
    }

    private function getArrayFrom(int|string|array $argument): array
    {
        return (!is_array($argument)) ? preg_split('/ ?[,|] ?/', (string) $argument) : $argument;
    }

    public function callMagic(string $method, array $parameters): mixed
    {
        if (Str::startsWith($method, 'is')) {
            return $this->hasRole(Str::snake(substr($method, 2), config('roles.separator')));
        } elseif (Str::startsWith($method, 'can')) {
            return $this->hasPermission(Str::snake(substr($method, 3), config('roles.separator')));
        } elseif (Str::startsWith($method, 'allowed')) {
            return $this->allowed(
                Str::snake(substr($method, 7), config('roles.separator')),
                $parameters[0],
                $parameters[1] ?? true,
                $parameters[2] ?? 'user_id'
            );
        }

        return parent::__call($method, $parameters);
    }

    public function __call($method, $parameters)
    {
        return $this->callMagic($method, $parameters);
    }
}
