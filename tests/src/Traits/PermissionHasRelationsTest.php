<?php

namespace Endone777\Roles\Tests\Traits;

use Endone777\Roles\Models\Permission;
use Endone777\Roles\Models\Role;
use Endone777\Roles\Tests\TestCase;
use Endone777\Roles\Tests\User;

class PermissionHasRelationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->runMigrations();
    }

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('auth.providers.users.model', User::class);
    }

    public function test_permission_has_roles(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();
        $role->permissions()->attach($permission);
        $this->assertEquals($role->id, $permission->roles->first()->id);
        $this->assertEquals($role->slug, $permission->roles->first()->slug);
    }

    public function test_permission_has_users(): void
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create();
        $user->userPermissions()->attach($permission);
        $this->assertEquals($user->id, $permission->users->first()->id);
        $this->assertEquals($user->name, $permission->users->first()->name);
    }
}
