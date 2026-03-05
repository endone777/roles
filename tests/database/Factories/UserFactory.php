<?php

namespace Endone777\Roles\Tests\Database\Factories;

use Endone777\Roles\Tests\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('secret'),
            'remember_token' => Str::random(10),
        ];
    }
}
