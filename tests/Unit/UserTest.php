<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_admin(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isEditor());
    }

    public function test_user_is_editor(): void
    {
        $editor = User::create([
            'name' => 'Editor User',
            'email' => 'editor@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Editor,
        ]);

        $this->assertTrue($editor->isEditor());
        $this->assertFalse($editor->isAdmin());
    }

    public function test_user_role_defaults_to_editor(): void
    {
        $user = User::create([
            'name' => 'New User',
            'email' => 'new@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertEquals(UserRole::Editor, $user->role);
        $this->assertTrue($user->isEditor());
    }

    public function test_user_role_enum_has_correct_values(): void
    {
        $this->assertEquals('admin', UserRole::Admin->value);
        $this->assertEquals('editor', UserRole::Editor->value);
    }

    public function test_user_role_enum_has_labels(): void
    {
        $this->assertEquals('Admin', UserRole::Admin->getLabel());
        $this->assertEquals('Editor', UserRole::Editor->getLabel());
    }
}
