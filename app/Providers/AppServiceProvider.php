<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Media;
use App\Models\Page;
use App\Policies\ArticlePolicy;
use App\Policies\MediaPolicy;
use App\Policies\PagePolicy;
use App\View\Composers\NavigationComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
        Page::class => PagePolicy::class,
        Media::class => MediaPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.layouts.app', NavigationComposer::class);

        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
