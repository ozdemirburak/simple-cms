<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

function createUser(UserRole $role = UserRole::Admin, array $attributes = []): User
{
    $user = User::create(array_merge([
        'name' => 'Test User',
        'email' => fake()->unique()->safeEmail(),
        'password' => bcrypt('password'),
    ], $attributes));

    $user->role = $role;
    $user->save();

    return $user->fresh();
}
