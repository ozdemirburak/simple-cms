<?php

use App\Enums\UserRole;

it('loads the admin login page', function () {
    $this->get('/admin/login')->assertOk();
});

it('redirects guests from admin dashboard', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('allows admin to access dashboard', function () {
    $this->actingAs(createUser(UserRole::Admin))
        ->get('/admin')
        ->assertOk();
});

it('allows editor to access dashboard', function () {
    $this->actingAs(createUser(UserRole::Editor))
        ->get('/admin')
        ->assertOk();
});

// Resource access
it('allows admin to access all resources', function (string $path) {
    $this->actingAs(createUser(UserRole::Admin))
        ->get("/admin/{$path}")
        ->assertOk();
})->with([
    'articles',
    'categories',
    'pages',
    'users',
    'media',
    'article-views',
]);

it('allows editor to access content resources', function (string $path) {
    $this->actingAs(createUser(UserRole::Editor))
        ->get("/admin/{$path}")
        ->assertOk();
})->with([
    'articles',
    'categories',
    'pages',
]);

it('denies editor access to users resource', function () {
    $this->actingAs(createUser(UserRole::Editor))
        ->get('/admin/users')
        ->assertForbidden();
});

it('denies editor access to article views', function () {
    $this->actingAs(createUser(UserRole::Editor))
        ->get('/admin/article-views')
        ->assertForbidden();
});

// Create page access
it('allows admin to access all create pages', function (string $path) {
    $this->actingAs(createUser(UserRole::Admin))
        ->get("/admin/{$path}/create")
        ->assertOk();
})->with([
    'articles',
    'categories',
    'pages',
    'users',
]);

it('allows editor to access content create pages', function (string $path) {
    $this->actingAs(createUser(UserRole::Editor))
        ->get("/admin/{$path}/create")
        ->assertOk();
})->with([
    'articles',
    'categories',
    'pages',
]);

it('denies editor access to create user page', function () {
    $this->actingAs(createUser(UserRole::Editor))
        ->get('/admin/users/create')
        ->assertForbidden();
});

it('denies editor access to edit user page', function () {
    $admin = createUser(UserRole::Admin);

    $this->actingAs(createUser(UserRole::Editor))
        ->get("/admin/users/{$admin->id}/edit")
        ->assertForbidden();
});
