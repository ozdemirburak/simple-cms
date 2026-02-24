<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Usage:
     *   php artisan db:seed                    - Seeds admin user only
     *   php artisan db:seed --class=ContentSeeder - Seeds sample content only
     *   php artisan migrate:fresh --seed      - Fresh database with admin user
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Seeder should not be run in production. Use "php artisan tinker" to create users manually.');

            return;
        }

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );
        $admin->role = UserRole::Admin;
        $admin->save();

        // Create editor user
        $editor = User::firstOrCreate(
            ['email' => 'editor@editor.com'],
            [
                'name' => 'Editor',
                'password' => bcrypt('password'),
            ]
        );
        $editor->role = UserRole::Editor;
        $editor->save();

        // To seed with sample content, run: php artisan db:seed --class=ContentSeeder
    }
}
