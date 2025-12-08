# Laravel Simple CMS

A beginner-friendly content management system built with Laravel 12, Filament 4, DaisyUI 5, and Lucide Icons.
Designed as a learning resource and starter template for those new to Laravel or Filament. Works great 
with AI coding assistants like Claude Code.

## Features

- **Admin Panel** - Beautiful admin interface powered by Filament 4
- **Content Management** - Manage articles, categories, and pages
- **Media Library** - Manage uploaded images with Spatie Media Library
- **Analytics Dashboard** - Track article views with charts and statistics
- **User Roles** - Admin and Editor roles with different permissions
- **Modern Frontend** - Clean, editorial-style design using DaisyUI 5, Tailwind CSS 4, and Lucide Icons

## Screenshots

<table>
  <tr>
    <td width="50%">
      <p align="center"><strong>Homepage</strong></p>
      <img src="https://github.com/user-attachments/assets/0373f16e-44f2-47f4-bc41-a735de13f263" alt="Homepage" />
    </td>
    <td width="50%">
      <p align="center"><strong>Admin Panel</strong></p>
      <img src="https://github.com/user-attachments/assets/54f17fb7-ee7f-4291-b1aa-0bb3be35cbc5" alt="Admin Panel" />
    </td>
  </tr>
</table>

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+
- npm or yarn

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/laravel-simple-cms.git
cd laravel-simple-cms
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies

```bash
npm install
```

### 4. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Database setup

The default configuration uses SQLite. The database file will be created automatically.

```bash
php artisan migrate
```

### 6. Seed the database

**Option A: Admin user only (minimal setup)**

```bash
php artisan db:seed
```

This creates:

**Admin user:**
- Email: `admin@admin.com`
- Password: `password`

**Editor user:**
- Email: `editor@editor.com`
- Password: `password`

**Option B: Admin user + Sample content**

```bash
php artisan db:seed
php artisan db:seed --class=ContentSeeder
```

This adds sample categories, articles, and pages (Privacy Policy, Terms of Service, About, Contact).

### 7. Storage setup

Create the symbolic link for public storage (required for image uploads):

```bash
php artisan storage:link
```

### 8. Build frontend assets

```bash
npm run dev
```

For production:

```bash
npm run build
```

### 9. Start the development server

```bash
php artisan serve
```

Or use Laravel Valet/Herd for a custom domain like `cms.test`.

## Usage

### Admin Panel

Access the admin panel at `/admin`:

- **URL**: http://localhost:8000/admin

**Admin login** (full access):
- Email: admin@admin.com
- Password: password

**Editor login** (no user management):
- Email: editor@editor.com
- Password: password

### Content Types

#### Categories

Categories organize your articles by topic. Each category has:

- Title
- Slug (auto-generated)
- Description
- Active status

#### Articles

Articles are the main content type. Each article has:

- Title
- Slug (auto-generated)
- Excerpt (optional summary)
- Content (rich text editor)
- Featured image
- Category (optional)
- Published status and date
- View count tracking

#### Pages

Static pages for content like About, Contact, Privacy Policy, etc. Each page has:

- Title
- Slug (auto-generated)
- Excerpt (optional summary)
- Content (rich text editor)
- Parent page (for hierarchical structure)
- Sort order (for navigation)
- Published status

### User Roles

The CMS supports two user roles:

- **Admin** - Full access to all features including user management
- **Editor** - Can manage articles, categories, and pages (no user management)

### Analytics Dashboard

The admin dashboard includes:

- **Stats Overview** - Total views, articles, categories, pages, and users
- **Article Views Chart** - Line chart showing daily views over 30 days
- **Top Articles** - Most viewed articles table
- **Recent Articles** - Latest article updates

### Admin Resources

- **Articles** - CRUD for articles with view count display
- **Categories** - CRUD for categories
- **Pages** - CRUD for static pages
- **Users** - User management (Admin only)
- **Media Library** - Browse and upload images (powered by Spatie Media Library)
- **Article Views** - Detailed view logs with filtering (under Analytics)

### Media Library

The Media Library allows you to:
- Upload multiple images at once with drag & drop
- Reorder images before uploading
- Edit images (crop, rotate) using the built-in image editor
- View all uploaded images across the CMS
- Preview images with details (size, type, URL)
- Copy image URLs for use in content
- Filter by collection or model type
- Delete unused images

Images are stored in `storage/app/public/` and managed by [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) with [Filament's plugin](https://filamentphp.com/plugins/filament-spatie-media-library).

### Frontend Routes

| Route              | Description                                 |
|--------------------|---------------------------------------------|
| `/`                | Home page with featured and latest articles |
| `/articles`        | All articles listing with category filters  |
| `/article/{slug}`  | Individual article page                     |
| `/category/{slug}` | Articles filtered by category               |
| `/page/{slug}`     | Static page                                 |

## Developer Guide

### Adding a New Resource (Model + Admin + Frontend)

This guide shows how to add a new resource (e.g., "Product") to the CMS.

#### Step 1: Create the Model and Migration

```bash
php artisan make:model Product -m
```

Edit the migration file in `database/migrations/`:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

Edit the model in `app/Models/Product.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Auto-generate slug from title
    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    // Scope for active products
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

Run the migration:

```bash
php artisan migrate
```

#### Step 2: Create Filament Admin Resource

Create the resource directory structure:

```
app/Filament/Resources/Products/
├── ProductResource.php
├── Pages/
│   ├── CreateProduct.php
│   ├── EditProduct.php
│   └── ListProducts.php
├── Schemas/
│   └── ProductForm.php
└── Tables/
    └── ProductsTable.php
```

**ProductResource.php:**

```php
<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
```

**Schemas/ProductForm.php:**

```php
<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make()
                    ->schema([
                        RichEditor::make('description'),
                    ]),
            ]);
    }
}
```

**Tables/ProductsTable.php:**

```php
<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('price')->money('USD')->sortable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
```

**Pages/ListProducts.php, CreateProduct.php, EditProduct.php:**

```php
// ListProducts.php
<?php
namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;
}

// CreateProduct.php
<?php
namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}

// EditProduct.php
<?php
namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
```

#### Step 3: Create Frontend Controller and Views

**app/Http/Controllers/Frontend/ProductController.php:**

```php
<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::active()->latest()->paginate(12);
        return view('frontend.products.index', compact('products'));
    }

    public function show(string $slug)
    {
        $product = Product::active()->where('slug', $slug)->firstOrFail();
        return view('frontend.products.show', compact('product'));
    }
}
```

**routes/web.php** - Add routes:

```php
use App\Http\Controllers\Frontend\ProductController;

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');
```

**resources/views/frontend/products/index.blade.php:**

```blade
<x-layouts.app title="Products">
    <section class="container mx-auto px-6 py-12">
        <h1 class="font-display text-4xl font-semibold mb-8">Products</h1>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($products as $product)
                <a href="{{ route('products.show', $product->slug) }}" class="card border border-base-200 hover:border-primary">
                    <div class="card-body">
                        <h2 class="card-title">{{ $product->title }}</h2>
                        <p class="text-primary font-semibold">${{ $product->price }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        {{ $products->links() }}
    </section>
</x-layouts.app>
```

#### Step 4: Add Language Strings (Optional)

Add to `lang/en/frontend.php`:

```php
'products' => [
    'title' => 'Products',
    'no_products' => 'No products found.',
],
```

### Filament 4 Important Notes

**Component Namespaces:**

```php
// Layout components - use Schemas\Components
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;

// Form input components - use Forms\Components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
```

**Property Types:**

```php
// Navigation icon uses BackedEnum
protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocument;

// Navigation group uses UnitEnum
protected static string|\UnitEnum|null $navigationGroup = 'Content';
```

**Widget Properties (instance, not static):**

```php
// CORRECT - instance properties
protected ?string $heading = 'Chart Title';
protected ?string $description = 'Description';
protected int|string|array $columnSpan = 'full';

// WRONG - do NOT use static for these
// protected static ?string $heading = 'Title';
```

## Customization

### Theme

The frontend uses a custom DaisyUI theme called "editorial". You can modify it in `resources/css/app.css`:

```css
@plugin "daisyui/theme" {
  name: "editorial";
  default: true;
  color-scheme: light;

  --color-primary: oklch(45% 0.15 25);
  /* ... other colors */
}
```

### Icons

The frontend uses [Blade Lucide Icons](https://github.com/mallardduck/blade-lucide-icons) for consistent iconography:

```blade
<x-lucide-arrow-right class="w-4 h-4" />
<x-lucide-newspaper class="w-10 h-10 text-base-content/30" />
<x-lucide-map-pin class="h-5 w-5 text-primary" />
```

Browse available icons at [lucide.dev](https://lucide.dev/icons/).

### Internationalization

All frontend text uses Laravel's localization system. Strings are stored in `lang/en/frontend.php`.

To add a new language:

1. Create a new directory: `lang/fr/`
2. Copy `lang/en/frontend.php` to `lang/fr/frontend.php`
3. Translate the strings
4. Set `APP_LOCALE=fr` in `.env`

## Project Structure

```
├── app/
│   ├── Filament/          # Filament admin resources
│   │   └── Resources/     # CRUD resources for models
│   ├── Http/
│   │   └── Controllers/   # Frontend controllers
│   └── Models/            # Eloquent models
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── resources/
│   ├── css/
│   │   └── app.css        # Tailwind + DaisyUI styles
│   └── views/
│       ├── components/
│       │   └── layouts/   # Layout components
│       └── frontend/      # Frontend views
└── routes/
    └── web.php            # Web routes
```

## Key Files

| File                                               | Purpose                                              |
|----------------------------------------------------|------------------------------------------------------|
| `app/Models/Article.php`                           | Article model with published scope and view tracking |
| `app/Models/ArticleView.php`                       | Article view tracking model                          |
| `app/Models/Category.php`                          | Category model                                       |
| `app/Models/Page.php`                              | Page model with hierarchical support                 |
| `app/Filament/Resources/`                          | Admin CRUD interfaces                                |
| `app/Filament/Widgets/`                            | Dashboard widgets (stats, charts, tables)            |
| `resources/css/app.css`                            | Custom DaisyUI theme and styles                      |
| `resources/views/components/layouts/app.blade.php` | Main layout                                          |
| `lang/en/frontend.php`                             | Frontend language strings (fully internationalized)  |
| `database/seeders/ContentSeeder.php`               | Sample content seeder with view data                 |

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit
```

The test suite includes:
- **Unit Tests** - Model tests for Article, Category, Page, User, ArticleView
- **Feature Tests** - Frontend routes, admin panel access, role-based permissions

## Commands Reference

```bash
# Development
php artisan serve              # Start development server
npm run dev                    # Start Vite dev server

# Database
php artisan migrate            # Run migrations
php artisan migrate:fresh      # Fresh migration (drops all tables)
php artisan db:seed            # Seed admin and editor users
php artisan db:seed --class=ContentSeeder  # Seed sample content

# Testing
./vendor/bin/phpunit           # Run all tests
./vendor/bin/phpunit --filter=TestName  # Run specific test

# Production
npm run build                  # Build assets for production
php artisan optimize           # Cache config, routes, views

# Filament
php artisan filament:user      # Create new admin user
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
