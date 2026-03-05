<?php

namespace Endone777\Roles\Tests\Database\Factories;

use Endone777\Roles\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'slug' => fake()->unique()->slug(2),
            'description' => '',
            'model' => fake()->word(),
        ];
    }
}
