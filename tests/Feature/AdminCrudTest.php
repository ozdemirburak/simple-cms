<?php

use App\Enums\UserRole;
use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Pages\Pages\CreatePage as CreatePagePage;
use App\Filament\Resources\Pages\Pages\EditPage as EditPagePage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\User;

use Livewire\Livewire;

beforeEach(function () {
    $this->admin = createUser(UserRole::Admin);
    $this->actingAs($this->admin);
});

// ── Categories ───────────────────────────────────────────────────

describe('Categories', function () {
    it('can be created', function () {
        Livewire::test(CreateCategory::class)
            ->fillForm([
                'title' => 'Technology',
                'slug' => 'technology',
                'description' => 'Tech articles',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', ['title' => 'Technology', 'slug' => 'technology']);
    });

    it('can be edited', function () {
        $category = Category::create(['title' => 'Old Title', 'slug' => 'old-title', 'is_active' => true]);

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->fillForm(['title' => 'Updated Title', 'slug' => 'updated-title'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'title' => 'Updated Title']);
    });

    it('can be deleted', function () {
        $category = Category::create(['title' => 'To Delete', 'slug' => 'to-delete', 'is_active' => true]);

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->callAction('delete');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    });

    it('appears in the list', function () {
        $category = Category::create(['title' => 'Listed', 'slug' => 'listed', 'is_active' => true]);

        Livewire::test(ListCategories::class)
            ->assertCanSeeTableRecords([$category]);
    });

    it('requires a title', function () {
        Livewire::test(CreateCategory::class)
            ->fillForm(['title' => '', 'slug' => 'no-title'])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required']);
    });

    it('requires a unique slug', function () {
        Category::create(['title' => 'Existing', 'slug' => 'existing-slug', 'is_active' => true]);

        Livewire::test(CreateCategory::class)
            ->fillForm(['title' => 'Another', 'slug' => 'existing-slug'])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    });
});

// ── Articles ─────────────────────────────────────────────────────

describe('Articles', function () {
    it('can be created with a category', function () {
        $category = Category::create(['title' => 'Tech', 'slug' => 'tech', 'is_active' => true]);

        Livewire::test(CreateArticle::class)
            ->fillForm([
                'title' => 'My First Article',
                'slug' => 'my-first-article',
                'content' => '<p>Body</p>',
                'category_id' => $category->id,
                'is_published' => true,
                'published_at' => now()->toDateTimeString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('articles', ['title' => 'My First Article', 'category_id' => $category->id]);
    });

    it('can be created without a category', function () {
        Livewire::test(CreateArticle::class)
            ->fillForm([
                'title' => 'No Category',
                'slug' => 'no-category',
                'content' => '<p>Content</p>',
                'is_published' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('articles', ['title' => 'No Category', 'category_id' => null]);
    });

    it('can be edited', function () {
        $article = Article::create(['title' => 'Original', 'slug' => 'original', 'content' => '<p>Old</p>', 'is_published' => false]);

        Livewire::test(EditArticle::class, ['record' => $article->getRouteKey()])
            ->fillForm(['title' => 'Updated', 'slug' => 'updated', 'is_published' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'title' => 'Updated', 'is_published' => true]);
    });

    it('can be deleted', function () {
        $article = Article::create(['title' => 'Delete Me', 'slug' => 'delete-me', 'content' => 'C', 'is_published' => false]);

        Livewire::test(EditArticle::class, ['record' => $article->getRouteKey()])
            ->callAction('delete');

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    });

    it('appears in the list', function () {
        $article = Article::create(['title' => 'Listed', 'slug' => 'listed', 'content' => 'C', 'is_published' => true, 'published_at' => now()]);

        Livewire::test(ListArticles::class)
            ->assertCanSeeTableRecords([$article]);
    });

    it('requires title and slug', function () {
        Livewire::test(CreateArticle::class)
            ->fillForm(['title' => '', 'slug' => ''])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required', 'slug' => 'required']);
    });

    it('requires a unique slug', function () {
        Article::create(['title' => 'Existing', 'slug' => 'existing-slug', 'content' => 'C', 'is_published' => false]);

        Livewire::test(CreateArticle::class)
            ->fillForm(['title' => 'Another', 'slug' => 'existing-slug'])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    });
});

// ── Pages ────────────────────────────────────────────────────────

describe('Pages', function () {
    it('can be created', function () {
        Livewire::test(CreatePagePage::class)
            ->fillForm([
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<p>About us content</p>',
                'is_published' => true,
                'sort_order' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('pages', ['title' => 'About Us', 'slug' => 'about-us']);
    });

    it('can be created as a child page', function () {
        $parent = Page::create(['title' => 'Parent', 'slug' => 'parent', 'is_published' => true]);

        Livewire::test(CreatePagePage::class)
            ->fillForm([
                'title' => 'Child Page',
                'slug' => 'child-page',
                'parent_id' => $parent->id,
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('pages', ['title' => 'Child Page', 'parent_id' => $parent->id]);
    });

    it('can be edited', function () {
        $page = Page::create(['title' => 'Old', 'slug' => 'old', 'content' => '<p>Old</p>', 'is_published' => false]);

        Livewire::test(EditPagePage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['title' => 'Updated', 'slug' => 'updated', 'is_published' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('pages', ['id' => $page->id, 'title' => 'Updated', 'is_published' => true]);
    });

    it('can be deleted', function () {
        $page = Page::create(['title' => 'Delete Me', 'slug' => 'delete-me', 'is_published' => false]);

        Livewire::test(EditPagePage::class, ['record' => $page->getRouteKey()])
            ->callAction('delete');

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    });

    it('appears in the list', function () {
        $page = Page::create(['title' => 'Listed', 'slug' => 'listed', 'is_published' => true]);

        Livewire::test(ListPages::class)
            ->assertCanSeeTableRecords([$page]);
    });

    it('requires title and slug', function () {
        Livewire::test(CreatePagePage::class)
            ->fillForm(['title' => '', 'slug' => ''])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required', 'slug' => 'required']);
    });
});

// ── Users ────────────────────────────────────────────────────────

describe('Users', function () {
    it('can be created as editor', function () {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'New Editor',
                'email' => 'editor@test.com',
                'password' => 'securepassword',
                'password_confirmation' => 'securepassword',
                'role' => 'editor',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'editor@test.com')->first();
        expect($user)->not->toBeNull()
            ->and($user->role)->toBe(UserRole::Editor);
    });

    it('can be created as admin', function () {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'New Admin',
                'email' => 'newadmin@test.com',
                'password' => 'securepassword',
                'password_confirmation' => 'securepassword',
                'role' => 'admin',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'newadmin@test.com')->first();
        expect($user->role)->toBe(UserRole::Admin);
    });

    it('can be edited', function () {
        $editor = createUser(UserRole::Editor, ['name' => 'Old Name', 'email' => 'old@test.com']);

        Livewire::test(EditUser::class, ['record' => $editor->getRouteKey()])
            ->fillForm(['name' => 'New Name', 'email' => 'new@test.com'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', ['id' => $editor->id, 'name' => 'New Name', 'email' => 'new@test.com']);
    });

    it('can be deleted', function () {
        $user = createUser(UserRole::Editor, ['email' => 'deleteme@test.com']);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->callAction('delete');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    });

    it('appears in the list', function () {
        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords([$this->admin]);
    });

    it('requires name, email, and password', function () {
        Livewire::test(CreateUser::class)
            ->fillForm(['name' => '', 'email' => '', 'password' => ''])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required', 'email' => 'required', 'password' => 'required']);
    });

    it('requires unique email', function () {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Duplicate',
                'email' => $this->admin->email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'editor',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique']);
    });

    it('requires password confirmation', function () {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Test',
                'email' => 'test@test.com',
                'password' => 'password123',
                'password_confirmation' => 'different',
                'role' => 'editor',
            ])
            ->call('create')
            ->assertHasFormErrors(['password' => 'confirmed']);
    });

    it('requires minimum password length', function () {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Test',
                'email' => 'short@test.com',
                'password' => 'short',
                'password_confirmation' => 'short',
                'role' => 'editor',
            ])
            ->call('create')
            ->assertHasFormErrors(['password' => 'min']);
    });

    it('cannot delete self', function () {
        Livewire::test(EditUser::class, ['record' => $this->admin->getRouteKey()])
            ->assertActionHidden('delete');
    });
});

// ── Editor CRUD ──────────────────────────────────────────────────

describe('Editor role', function () {
    beforeEach(function () {
        $this->actingAs(createUser(UserRole::Editor));
    });

    it('can create articles', function () {
        Livewire::test(CreateArticle::class)
            ->fillForm(['title' => 'Editor Article', 'slug' => 'editor-article', 'content' => '<p>Content</p>', 'is_published' => true, 'published_at' => now()->toDateTimeString()])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('articles', ['title' => 'Editor Article']);
    });

    it('can create categories', function () {
        Livewire::test(CreateCategory::class)
            ->fillForm(['title' => 'Editor Category', 'slug' => 'editor-category', 'is_active' => true])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', ['title' => 'Editor Category']);
    });

    it('can create pages', function () {
        Livewire::test(CreatePagePage::class)
            ->fillForm(['title' => 'Editor Page', 'slug' => 'editor-page', 'is_published' => true])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('pages', ['title' => 'Editor Page']);
    });
});
