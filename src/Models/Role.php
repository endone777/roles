<?php

namespace Endone777\Roles\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Endone777\Roles\Contracts\RoleHasRelations as RoleHasRelationsContract;
use Endone777\Roles\Traits\RoleHasRelations;
use Endone777\Roles\Traits\Slugable;

class Role extends Model implements RoleHasRelationsContract
{
    use HasFactory, Slugable, RoleHasRelations;

    protected $fillable = ['name', 'slug', 'description', 'level'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($connection = config('roles.connection')) {
            $this->connection = $connection;
        }
    }

    protected static function newFactory(): \Endone777\Roles\Tests\Database\Factories\RoleFactory
    {
        return \Endone777\Roles\Tests\Database\Factories\RoleFactory::new();
    }
}
