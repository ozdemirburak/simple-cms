<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\ArticleView;
use App\Models\Category;
use App\Models\Page;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
    {
        $totalViews = ArticleView::count();
        $todayViews = ArticleView::whereDate('viewed_at', today())->count();
        $weekViews = ArticleView::where('viewed_at', '>=', now()->subWeek())->count();

        return [
            Stat::make('Total Views', Number::abbreviate($totalViews))
                ->description('Today: ' . $todayViews . ' | This week: ' . $weekViews)
                ->color('primary'),
            Stat::make('Total Articles', Article::count())
                ->description('Published: ' . Article::where('is_published', true)->count())
                ->color('success'),
            Stat::make('Total Categories', Category::count())
                ->description('Active: ' . Category::where('is_active', true)->count())
                ->color('info'),
            Stat::make('Total Pages', Page::count())
                ->description('Published: ' . Page::where('is_published', true)->count())
                ->color('warning'),
            Stat::make('Total Users', User::count())
                ->description('Admins: ' . User::where('role', 'admin')->count() . ' | Editors: ' . User::where('role', 'editor')->count())
                ->color('gray'),
        ];
    }
}
