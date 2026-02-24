<?php

use App\Enums\UserRole;
use App\Models\User;

it('identifies admin users', function () {
    $admin = createUser(UserRole::Admin);

    expect($admin->isAdmin())->toBeTrue()
        ->and($admin->isEditor())->toBeFalse();
});

it('identifies editor users', function () {
    $editor = createUser(UserRole::Editor);

    expect($editor->isEditor())->toBeTrue()
        ->and($editor->isAdmin())->toBeFalse();
});

it('defaults role to editor', function () {
    $user = User::create([
        'name' => 'New User',
        'email' => 'new@test.com',
        'password' => bcrypt('password'),
    ]);

    expect($user->role)->toBe(UserRole::Editor);
});

it('has correct enum values', function () {
    expect(UserRole::Admin->value)->toBe('admin')
        ->and(UserRole::Editor->value)->toBe('editor');
});

it('has labels on enum', function () {
    expect(UserRole::Admin->getLabel())->not->toBeEmpty()
        ->and(UserRole::Editor->getLabel())->not->toBeEmpty();
});

it('does not mass-assign role', function () {
    $user = User::create([
        'name' => 'Test',
        'email' => 'test@test.com',
        'password' => bcrypt('password'),
        'role' => UserRole::Admin,
    ]);

    expect($user->role)->toBe(UserRole::Editor);
});

it('requires admin or editor role for panel access', function () {
    $admin = createUser(UserRole::Admin);
    $editor = createUser(UserRole::Editor);

    $panel = app(\Filament\Panel::class);

    expect($admin->canAccessPanel($panel))->toBeTrue()
        ->and($editor->canAccessPanel($panel))->toBeTrue();
});
