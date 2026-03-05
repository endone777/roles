<?php

namespace Endone777\Roles\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Endone777\Roles\Contracts\PermissionHasRelations as PermissionHasRelationsContract;
use Endone777\Roles\Traits\PermissionHasRelations;
use Endone777\Roles\Traits\Slugable;

class Permission extends Model implements PermissionHasRelationsContract
{
    use HasFactory, Slugable, PermissionHasRelations;

    protected $fillable = ['name', 'slug', 'description', 'model'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($connection = config('roles.connection')) {
            $this->connection = $connection;
        }
    }

    protected static function newFactory(): \Endone777\Roles\Tests\Database\Factories\PermissionFactory
    {
        return \Endone777\Roles\Tests\Database\Factories\PermissionFactory::new();
    }
}
