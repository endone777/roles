<?php

namespace Endone777\Roles\Tests;

use Endone777\Roles\Traits\HasRoleAndPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, HasRoleAndPermission;

    protected $fillable = ['name', 'email', 'password'];

    protected static function newFactory(): \Endone777\Roles\Tests\Database\Factories\UserFactory
    {
        return \Endone777\Roles\Tests\Database\Factories\UserFactory::new();
    }
}
