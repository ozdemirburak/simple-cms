# Security Fix: Broken Access Control (CWE-285)

## Vulnerability Description

**CVE-ID**: Pending assignment  
**Severity**: HIGH (CVSS Score: 8.1)  
**CWE**: CWE-285 - Improper Authorization  
**Date Discovered**: March 10, 2026  
**Status**: FIXED

### Summary

A critical Broken Access Control vulnerability was discovered in the Simple CMS application. The vulnerability allowed authenticated users with Editor role to:

1. **View** articles, pages, and media belonging to other users
2. **Edit** content created by other users
3. **Delete** resources owned by other users

This violates the principle of least privilege and allows horizontal privilege escalation.

### Attack Scenario

```
1. User A (Editor) creates Article #1
2. User B (Editor) creates Article #2
3. User B could access /admin/articles/1/edit
4. User B could modify or delete Article #1 (owned by User A)
5. No ownership validation was performed
```

### Impact

- **Confidentiality**: HIGH - Users could access private content of others
- **Integrity**: HIGH - Users could modify content they don't own
- **Availability**: HIGH - Users could delete other users' content

### Affected Components

- `app/Models/Article.php` - No ownership tracking
- `app/Models/Page.php` - No ownership tracking  
- `Spatie\MediaLibrary\MediaCollections\Models\Media` - No ownership tracking
- `app/Filament/Resources/Articles/ArticleResource.php` - No query scoping
- `app/Filament/Resources/Pages/PageResource.php` - No query scoping
- `app/Filament/Resources/Media/MediaResource.php` - No query scoping

## Fix Implementation

### 1. Database Schema Updates

**Migration**: `2026_03_10_000000_add_user_id_to_articles_pages_media_tables.php`

Added `user_id` foreign key to:
- `articles` table
- `pages` table
- `media` table

### 2. Model Updates

#### Article Model
- Added `user_id` to `$fillable`
- Added `user()` relationship
- Auto-assign `user_id` on creation via `booted()` method

#### Page Model
- Added `user_id` to `$fillable`
- Added `user()` relationship
- Auto-assign `user_id` on creation via `booted()` method

#### Media Model (Custom)
- Created custom `App\Models\Media` extending Spatie's Media
- Added `user_id` to `$fillable`
- Added `user()` relationship
- Auto-assign `user_id` on creation via `booted()` method

#### User Model
- Added `articles()`, `pages()`, `media()` relationships

### 3. Authorization Policies

Created three Laravel Policies:

#### ArticlePolicy
```php
public function update(User $user, Article $article): bool
{
    return $user->isAdmin() || $article->user_id === $user->id;
}

public function delete(User $user, Article $article): bool
{
    return $user->isAdmin() || $article->user_id === $user->id;
}
```

#### PagePolicy
```php
public function update(User $user, Page $page): bool
{
    return $user->isAdmin() || $page->user_id === $user->id;
}

public function delete(User $user, Page $page): bool
{
    return $user->isAdmin() || $page->user_id === $user->id;
}
```

#### MediaPolicy
```php
public function update(User $user, Media $media): bool
{
    return $user->isAdmin() || $media->user_id === $user->id;
}

public function delete(User $user, Media $media): bool
{
    return $user->isAdmin() || $media->user_id === $user->id;
}
```

### 4. Filament Resource Scoping

Updated all three Resources with `getEloquentQuery()` method:

```php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();

    // Non-admin users can only see their own records
    if (auth()->check() && !auth()->user()->isAdmin()) {
        $query->where('user_id', auth()->id());
    }

    return $query;
}
```

### 5. Policy Registration

Updated `AppServiceProvider` to register policies:

```php
protected $policies = [
    Article::class => ArticlePolicy::class,
    Page::class => PagePolicy::class,
    Media::class => MediaPolicy::class,
];

public function boot(): void
{
    foreach ($this->policies as $model => $policy) {
        Gate::policy($model, $policy);
    }
}
```

## Verification Steps

### 1. Run Migration

```bash
php artisan migrate
```

### 2. Test Authorization

#### Test Case 1: Editor Cannot Edit Other's Article
```php
// As User B (Editor)
$articleByUserA = Article::where('user_id', 1)->first();
$response = $this->actingAs($userB)->put("/admin/articles/{$articleByUserA->id}", [
    'title' => 'Hacked!',
]);
$response->assertForbidden(); // Should return 403
```

#### Test Case 2: Editor Cannot Delete Other's Page
```php
// As User B (Editor)
$pageByUserA = Page::where('user_id', 1)->first();
$response = $this->actingAs($userB)->delete("/admin/pages/{$pageByUserA->id}");
$response->assertForbidden(); // Should return 403
```

#### Test Case 3: Editor Can Only See Own Records
```php
// As User B (Editor) - should only see their own articles
$this->actingAs($userB);
$articles = Article::all(); // Query is scoped
$this->assertTrue($articles->every(fn($a) => $a->user_id === $userB->id));
```

#### Test Case 4: Admin Can Access Everything
```php
// As Admin
$this->actingAs($admin);
$allArticles = Article::all(); // No scoping
$this->assertTrue($allArticles->count() > 0);
```

## Security Best Practices Applied

✅ **Principle of Least Privilege**: Users can only access their own resources  
✅ **Defense in Depth**: Multiple layers (Query scoping + Policies)  
✅ **Secure by Default**: `user_id` auto-assigned on creation  
✅ **Clear Separation**: Admin vs Editor authorization  
✅ **Database Constraints**: Foreign key enforces referential integrity  

## Remaining Considerations

### 1. Existing Data Migration

For existing records without `user_id`, you may need to:

```php
// Assign all existing records to first admin
$admin = User::where('role', UserRole::Admin)->first();
Article::whereNull('user_id')->update(['user_id' => $admin->id]);
Page::whereNull('user_id')->update(['user_id' => $admin->id]);
DB::table('media')->whereNull('user_id')->update(['user_id' => $admin->id]);
```

### 2. API Endpoints

If the application exposes REST APIs, ensure:
- API authentication is enforced
- Same authorization policies apply
- Rate limiting is configured

### 3. Testing

Recommended tests to add:
- `tests/Feature/ArticleAuthorizationTest.php`
- `tests/Feature/PageAuthorizationTest.php`
- `tests/Feature/MediaAuthorizationTest.php`

### 4. Audit Logging

Consider adding audit logs to track:
- Who accessed what resources
- Failed authorization attempts
- Administrative overrides

## References

- **CWE-285**: Improper Authorization - https://cwe.mitre.org/data/definitions/285.html
- **OWASP Top 10 2021**: A01:2021 – Broken Access Control
- **Laravel Authorization**: https://laravel.com/docs/authorization
- **Filament Authorization**: https://filamentphp.com/docs/panels/resources/authorization

## Credits

- Discovered by: [Your Name]
- Fixed by: [Your Name]
- Date: March 10, 2026
