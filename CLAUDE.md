# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A simple content management system built with Laravel 12, Filament PHP 5.2 (admin panel), DaisyUI 5 (frontend), and Lucide Icons. Manages Pages, Articles, Categories, and Media uploads with Spatie Media Library. Frontend is fully internationalized. Content is sanitized with stevebauman/purify to prevent XSS.

## Common Commands

```bash
# Development
composer install          # Install PHP dependencies
npm install               # Install Node dependencies
npm run dev               # Start Vite dev server with HMR
npm run build             # Build production assets
php artisan serve         # Start Laravel dev server at localhost:8000

# Database
php artisan migrate       # Run migrations
php artisan migrate:fresh --seed  # Reset DB and seed
php artisan db:seed       # Seed database with admin and editor users

# Testing
./vendor/bin/pest      # Run all tests
./vendor/bin/pest --filter=TestName  # Run specific test

# Filament
php artisan make:filament-resource ModelName --generate  # Create CRUD resource
php artisan filament:upgrade  # Upgrade Filament assets

# Cache
php artisan optimize:clear  # Clear all caches
```

## Architecture

### Admin Panel (Filament PHP 5.2)
- **Location**: `app/Filament/Resources/`
- **Panel Provider**: `app/Providers/Filament/AdminPanelProvider.php`
- **Access**: `/admin` (admin: admin@admin.com / password, editor: editor@editor.com / password)
- Resources follow Filament v5 structure with separate Schemas/ and Tables/ directories
- `canAccessPanel()` restricts access to admin and editor roles only

### Filament 5 Resource Property Types
When creating Filament resources, use these property type declarations:
```php
protected static ?string $model = Model::class;
protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
protected static ?int $navigationSort = 1;
```

Note: In Filament 5, `$navigationIcon` uses `BackedEnum` (not `UnitEnum`). Import with `use BackedEnum;`.
Note: `$navigationGroup` still uses `\UnitEnum|string|null` type (PHP property variance requirement).

### Filament 5 Component Namespaces
**IMPORTANT**: In Filament 5, layout/structural components are in `Filament\Schemas\Components`:
```php
// Layout components - in Schemas\Components (NOT Forms\Components!)
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;

// Form input components - still in Forms\Components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;

// Schema class
use Filament\Schemas\Schema;

// Utilities
use Filament\Schemas\Components\Utilities\Get;
```

### Filament 5 Resource Directory Structure
When creating a resource, Filament 5 generates this structure:
```
app/Filament/Resources/
└── Customers/
    ├── CustomerResource.php
    ├── Pages/
    │   ├── CreateCustomer.php
    │   ├── EditCustomer.php
    │   └── ListCustomers.php
    ├── Schemas/
    │   └── CustomerForm.php
    └── Tables/
        └── CustomersTable.php
```

### Filament 5 Form Schema Pattern
```php
// In Schemas/CustomerForm.php
namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required(),
            ]);
    }
}
```

### Filament 5 Widget Properties
Widget class properties are **instance properties**, NOT static:
```php
// CORRECT - instance properties
protected ?string $heading = 'Chart Title';
protected ?string $description = 'Chart description';
protected ?string $maxHeight = '300px';
protected int|string|array $columnSpan = 'full';

// WRONG - do NOT use static
// protected static ?string $heading = 'Title';  // This will error!
```

### Filament 5 Panel Configuration
```php
use Filament\Support\Enums\Width;

public function panel(Panel $panel): Panel
{
    return $panel
        ->maxContentWidth(Width::Full)  // Full width content
        ->spa()  // Single-page application mode
        ->unsavedChangesAlerts()  // Warn before leaving unsaved forms
        ->databaseTransactions();  // Wrap operations in transactions
}
```

### Filament 5 Hiding Fields by Operation
```php
use Filament\Support\Enums\Operation;

TextInput::make('password')
    ->password()
    ->required()
    ->hiddenOn(Operation::Edit)  // Hide on edit page
    ->visibleOn(Operation::Create);  // Only show on create
```

### Frontend (DaisyUI 5 + Lucide Icons)
- **Controllers**: `app/Http/Controllers/Frontend/`
- **Views**: `resources/views/frontend/`
- **Layout**: `resources/views/components/layouts/app.blade.php`
- **Language**: `lang/en/frontend.php` (all text is internationalized)
- Uses DaisyUI component classes and Lucide icons (`<x-lucide-*>`)

### Models
- `Category` - hasMany Articles
- `Article` - belongsTo Category, has published scope, auto-generates slug, has views tracking
- `ArticleView` - tracks article views with IP, user agent, referer
- `Page` - self-referential (parent/children), has published scope, auto-generates slug
- `User` - implements FilamentUser, has role (admin/editor)
- `MediaItem` - implements HasMedia for standalone media uploads

### User Roles
Uses simple role field with `App\Enums\UserRole` enum:
- **Admin**: Full access, can manage users
- **Editor**: Can manage articles, categories, pages (no user management)
- `role` is NOT mass-assignable. Set it explicitly: `$user->role = UserRole::Admin; $user->save();`

```php
// Check role
$user->isAdmin();  // true if admin
$user->isEditor(); // true if editor
```

### Security Patterns
- **HTML Sanitization**: Article and Page content is sanitized via `stevebauman/purify` on save to prevent stored XSS
- **View Deduplication**: Article views are deduplicated per IP per 30 minutes via cache
- **Route Constraints**: All slug routes have `[a-z0-9\-]+` regex constraints
- **File Upload Validation**: Featured images require explicit MIME type allowlist
- **Mass Assignment**: `role` field is excluded from `$fillable` on User model to prevent privilege escalation

### Database
- SQLite by default (`database/database.sqlite`)
- Migrations: `database/migrations/`

## Key Patterns

### Slug Generation
Models auto-generate slugs from title on creation:
```php
static::creating(function ($model) {
    if (empty($model->slug)) {
        $model->slug = Str::slug($model->title);
    }
});
```

### Published Scopes
Articles and Pages have `published` scopes for filtering:
```php
Article::published()->get();  // is_published=true, published_at <= now
Page::published()->get();     // is_published=true
```

### Frontend Routes
```
/                    - Home (latest articles)
/articles            - Article listing
/article/{slug}      - Article detail
/category/{slug}     - Category articles
/page/{slug}         - Page detail
```

## DaisyUI 5 Guidelines

### Configuration (resources/css/app.css)
DaisyUI 5 uses CSS-based configuration with Tailwind CSS 4:
```css
@plugin "daisyui" {
    themes: false;
    exclude: properties;
}

/* Custom theme */
@plugin "daisyui/theme" {
    name: "editorial";
    default: true;
    color-scheme: light;
    --color-base-100: oklch(98% 0.006 90);
    --color-primary: oklch(40% 0.15 15);
    /* ... other colors */
}
```

### Key DaisyUI 5 Components

**Layout:**
- **navbar**: `navbar`, `navbar-start`, `navbar-center`, `navbar-end`
- **hero**: `hero`, `hero-content`, `hero-overlay`
- **footer**: `footer`, `footer-title`, `footer-center`
- **drawer**: `drawer`, `drawer-toggle`, `drawer-content`, `drawer-side`, `drawer-overlay`

**Display:**
- **card**: `card`, `card-body`, `card-title`, `card-actions` (sizes: `card-xs` to `card-xl`)
- **badge**: `badge`, colors + styles: `badge-primary`, `badge-soft`, `badge-ghost`, `badge-outline`
- **alert**: `alert`, `alert-info`, `alert-success`, `alert-warning`, `alert-error`
- **stat**: `stats`, `stat`, `stat-title`, `stat-value`, `stat-desc`

**Navigation:**
- **menu**: `menu`, `menu-horizontal`, `menu-title`, sizes: `menu-xs` to `menu-xl`
- **breadcrumbs**: `breadcrumbs` with `<ul><li><a>` structure
- **tabs**: `tabs`, `tab`, `tab-content`, styles: `tabs-box`, `tabs-border`, `tabs-lift`

**Actions:**
- **btn**: Colors + styles: `btn-primary`, `btn-ghost`, `btn-outline`, `btn-soft`, `btn-link`
  Sizes: `btn-xs`, `btn-sm`, `btn-md`, `btn-lg`, `btn-xl`
  Modifiers: `btn-wide`, `btn-block`, `btn-square`, `btn-circle`
- **dropdown**: `dropdown`, `dropdown-content`, placement: `dropdown-end`, `dropdown-top`

**Form:**
- **input**: `input`, `input-primary`, `input-ghost`, sizes: `input-xs` to `input-xl`
- **select**: `select`, colors and sizes like input
- **textarea**: `textarea`, colors and sizes like input
- **checkbox**: `checkbox`, `checkbox-primary`, sizes: `checkbox-xs` to `checkbox-xl`
- **toggle**: `toggle`, `toggle-primary`, sizes: `toggle-xs` to `toggle-xl`
- **label**: `label` for descriptions, `floating-label` for floating labels

**Utility:**
- **divider**: `divider`, `divider-horizontal`, `divider-vertical`
- **loading**: `loading`, styles: `loading-spinner`, `loading-dots`, `loading-ring`
- **modal**: `modal`, `modal-box`, `modal-action`, `modal-backdrop`

### DaisyUI 5 New Features
- **Soft style**: `badge-soft`, `btn-soft` for softer appearance
- **Dash style**: `badge-dash`, `card-dash` for dashed borders
- **XL size**: `btn-xl`, `badge-xl` now available
- **Effects**: `--depth` and `--noise` theme variables
- Colors use oklch() format for better customization

## Lucide Icons

The frontend uses [Blade Lucide Icons](https://github.com/mallardduck/blade-lucide-icons). Use the component syntax:

```blade
<x-lucide-arrow-right class="w-4 h-4" />
<x-lucide-newspaper class="w-10 h-10 text-base-content/30" />
<x-lucide-menu class="w-5 h-5" />
<x-lucide-eye class="h-5 w-5" />
<x-lucide-map-pin class="h-4 w-4" />
<x-lucide-mail class="h-5 w-5 text-primary" />
<x-lucide-clock class="h-5 w-5 text-primary" />
<x-lucide-file-text class="h-5 w-5 text-primary" />
<x-lucide-chevron-left class="h-4 w-4" />
<x-lucide-chevron-right class="h-4 w-4" />
```

Browse all icons at [lucide.dev/icons](https://lucide.dev/icons/).

### Theme Colors
Use semantic color classes:
- `bg-base-100/200/300` - Background levels
- `text-base-content` - Main text color
- `bg-primary`, `text-primary` - Primary accent
- `bg-neutral`, `text-neutral-content` - Footer/dark sections

## File Upload & Spatie Media Library

### Storage Setup
Run `php artisan storage:link` to create the public symlink for uploaded files.

### Spatie Media Library File Upload
Use `SpatieMediaLibraryFileUpload` for models that implement `HasMedia`:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

// Basic usage
SpatieMediaLibraryFileUpload::make('avatar')
    ->collection('avatars')  // Optional: group files into categories

// Full featured upload
SpatieMediaLibraryFileUpload::make('images')
    ->collection('images')
    ->multiple()
    ->reorderable()
    ->image()
    ->imageEditor()  // Enable crop/rotate
    ->maxSize(5120)
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
    ->responsiveImages()  // Generate responsive images
    ->conversion('thumb')  // Use specific conversion for preview
```

### Model Setup for Media Library
```php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile()
            ->useDisk('public');
    }
}
```

### Table Column for Media
```php
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar')
    ->collection('avatars')
    ->conversion('thumb')  // Use thumbnail conversion
    ->allCollections()  // Show from all collections
```

### Infolist Entry for Media
```php
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

SpatieMediaLibraryImageEntry::make('avatar')
    ->collection('avatars')
```

### Action Modal with File Upload
For uploading media in action modals, use regular FileUpload then attach via Spatie:
```php
use Filament\Forms\Components\FileUpload;

Action::make('upload')
    ->form([
        FileUpload::make('images')
            ->multiple()
            ->image()
            ->imageEditor(),
    ])
    ->action(function (array $data): void {
        $mediaItem = MediaItem::create(['name' => 'Upload']);
        foreach ($data['images'] as $image) {
            $mediaItem->addMedia(storage_path('app/private/livewire-tmp/' . $image))
                ->toMediaCollection('images');
        }
    })
```

### Infolist in Action Modal
Use Filament's native infolist for previews with copyable URLs:
```php
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

Action::make('view')
    ->infolist([
        ImageEntry::make('preview')
            ->hiddenLabel()
            ->state(fn ($record) => $record->getUrl())
            ->height(300),
        TextEntry::make('url')
            ->state(fn ($record) => $record->getUrl())
            ->copyable()
            ->copyMessage('Copied!'),
        Section::make('Details')
            ->schema([
                TextEntry::make('size'),
                TextEntry::make('type'),
            ])
            ->columns(2)
            ->compact(),
    ])
    ->modalSubmitAction(false)
```
