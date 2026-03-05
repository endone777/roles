<?php

namespace Endone777\Roles\Tests\Middleware;

use Endone777\Roles\Exceptions\RoleDeniedException;
use Endone777\Roles\Middleware\VerifyRole;
use Endone777\Roles\Tests\TestCase;
use Endone777\Roles\Tests\User;
use Illuminate\Http\Request;
use Mockery;

class VerifyRoleTest extends TestCase
{
    public function test_user_has_role(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('hasRole')->once()->with('role1')->andReturn(true);

        $this->actingAs($user);

        $middleware = new VerifyRole();
        $result = $middleware->handle(new Request(), function (Request $request) {
            return response('next was called');
        }, 'role1');

        $this->assertEquals('next was called', $result->getContent());
    }

    public function test_user_has_role_throws_exception(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('hasRole')->once()->with('role1')->andReturn(false);

        $this->actingAs($user);

        $this->expectException(RoleDeniedException::class);

        $middleware = new VerifyRole();
        $middleware->handle(new Request(), function (Request $request) {
        }, 'role1');
    }
}
