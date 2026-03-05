<?php

namespace Endone777\Roles\Tests\Traits;

use Endone777\Roles\Models\Permission;
use Endone777\Roles\Models\Role;
use Endone777\Roles\Tests\TestCase;
use Endone777\Roles\Tests\User;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class HasRoleAndPermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('auth.providers.users.model', User::class);
    }

    public function test_role_permissions(): void
    {
        $this->runMigrations();

        $user = User::factory()->make();

        $roles = new Collection([
            Role::factory()->create(),
            Role::factory()->create(),
            Role::factory()->create(['level' => 2]),
            Role::factory()->create(['level' => 3]),
        ]);

        $permissions = Permission::factory()->count(8)->create();

        $permissions->each(function ($permission, $key) use ($roles) {
            switch ($key) {
                case 0:
                case 1:
                    $roles->get(0)->attachPermission($permission);
                    break;
                case 2:
                case 3:
                    $roles->get(1)->attachPermission($permission);
                    break;
                case 4:
                case 5:
                    $roles->get(2)->attachPermission($permission);
                    break;
                case 6:
                case 7:
                    $roles->get(3)->attachPermission($permission);
                    break;
            }
        });

        $user->roles()->attach($roles->get(0));

        $this->assertEquals(
            $permissions->toBase()->only([0, 1])->pluck('id')->toArray(),
            $user->rolePermissions()->get()->pluck('id')->toArray()
        );

        $user->detachRole(null);

        $user->roles()->attach($roles->get(2));

        $this->assertEquals(
            $permissions->toBase()->only([0, 1, 2, 3, 4, 5])->pluck('id')->toArray(),
            $user->rolePermissions()->get()->pluck('id')->toArray()
        );
    }

    public function test_has_role(): void
    {
        $user = Mockery::mock(User::class . '[hasOneRole]');
        $user->shouldReceive('hasOneRole')
            ->with('role1')
            ->once()
            ->andReturn(true);
        $this->assertTrue($user->hasRole('role1'));
    }

    public function test_has_role_all(): void
    {
        $user = Mockery::mock(User::class . '[hasAllRoles]');
        $user->shouldReceive('hasAllRoles')
            ->with(['role1', 'role2'])
            ->once()
            ->andReturn(true);
        $this->assertTrue($user->hasRole(['role1', 'role2'], true));
    }

    public function test_has_one_role_true(): void
    {
        $user = Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(false);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(true);

        $this->assertTrue($user->hasOneRole(['role1', 'role2']));
    }

    public function test_has_one_role_false(): void
    {
        $user = Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(false);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(false);

        $this->assertFalse($user->hasOneRole(['role1', 'role2']));
    }

    public function test_has_all_roles_true(): void
    {
        $user = Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(true);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllRoles(['role1', 'role2']));
    }

    public function test_has_all_roles_false(): void
    {
        $user = Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(true);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(false);

        $this->assertFalse($user->hasAllRoles(['role1', 'role2']));
    }

    public function test_has_all_roles_csv(): void
    {
        $user = Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(true);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllRoles('role1,role2'));
    }

    public function test_has_all_roles_pipe(): void
    {
        $user = Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(true);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllRoles('role1| role2'));
    }

    public function test_check_role(): void
    {
        $user = Mockery::mock(User::class . '[getRoles]');
        $roles = Role::factory()->count(4)->make();
        $user->shouldReceive('getRoles')
            ->once()
            ->withNoArgs()
            ->andReturn($roles);

        $this->assertTrue($user->checkRole($roles->first()->id));
    }

    public function test_has_permission(): void
    {
        $user = Mockery::mock(User::class . '[hasOnePermission]');
        $user->shouldReceive('hasOnePermission')
            ->with('permission1')
            ->once()
            ->andReturn(true);
        $this->assertTrue($user->hasPermission('permission1'));
    }

    public function test_has_permission_all(): void
    {
        $user = Mockery::mock(User::class . '[hasAllPermissions]');
        $user->shouldReceive('hasAllPermissions')
            ->with(['permission1', 'permission2'])
            ->once()
            ->andReturn(true);
        $this->assertTrue($user->hasPermission(['permission1', 'permission2'], true));
    }

    public function test_has_one_permission_true(): void
    {
        $user = Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(false);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(true);

        $this->assertTrue($user->hasOnePermission(['permission1', 'permission2']));
    }

    public function test_has_one_permission_false(): void
    {
        $user = Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(false);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(false);

        $this->assertFalse($user->hasOnePermission(['permission1', 'permission2']));
    }

    public function test_has_all_permissions_true(): void
    {
        $user = Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(true);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllPermissions(['permission1', 'permission2']));
    }

    public function test_has_all_permissions_false(): void
    {
        $user = Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(true);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(false);

        $this->assertFalse($user->hasAllPermissions(['permission1', 'permission2']));
    }

    public function test_has_all_permissions_csv(): void
    {
        $user = Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(true);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllPermissions('permission1,permission2'));
    }

    public function test_has_all_permissions_pipe(): void
    {
        $user = Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(true);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllPermissions('permission1| permission2'));
    }

    public function test_check_permission(): void
    {
        $user = Mockery::mock(User::class . '[getPermissions]');
        $permissions = Permission::factory()->count(4)->make();
        $user->shouldReceive('getPermissions')
            ->once()
            ->withNoArgs()
            ->andReturn($permissions);

        $this->assertTrue($user->checkPermission($permissions->first()->id));
    }

    public function test_magic_call(): void
    {
        $user = Mockery::mock(User::class . '[hasRole,hasPermission]');

        $user->shouldReceive('hasRole')
            ->once()
            ->with('my.role')
            ->andReturn(true);
        $this->assertTrue($user->callMagic('isMyRole', []));

        $user->shouldReceive('hasPermission')
            ->once()
            ->with('my.permission')
            ->andReturn(true);
        $this->assertTrue($user->callMagic('canMyPermission', []));
    }
}
