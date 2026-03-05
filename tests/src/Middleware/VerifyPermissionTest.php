<?php

namespace Endone777\Roles\Tests\Middleware;

use Endone777\Roles\Exceptions\PermissionDeniedException;
use Endone777\Roles\Middleware\VerifyPermission;
use Endone777\Roles\Tests\TestCase;
use Endone777\Roles\Tests\User;
use Illuminate\Http\Request;
use Mockery;

class VerifyPermissionTest extends TestCase
{
    public function test_user_has_permission(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('hasPermission')->once()->with('permission1')->andReturn(true);

        $this->actingAs($user);

        $middleware = new VerifyPermission();
        $result = $middleware->handle(new Request(), function (Request $request) {
            return response('next was called');
        }, 'permission1');

        $this->assertEquals('next was called', $result->getContent());
    }

    public function test_user_has_permission_throws_exception(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('hasPermission')->once()->with('permission1')->andReturn(false);

        $this->actingAs($user);

        $this->expectException(PermissionDeniedException::class);

        $middleware = new VerifyPermission();
        $middleware->handle(new Request(), function (Request $request) {
        }, 'permission1');
    }
}
