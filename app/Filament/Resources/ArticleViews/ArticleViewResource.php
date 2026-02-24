<?php

namespace App\Filament\Resources\ArticleViews;

use App\Filament\Resources\ArticleViews\Pages\ListArticleViews;
use App\Filament\Resources\ArticleViews\Tables\ArticleViewsTable;
use App\Models\ArticleView;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ArticleViewResource extends Resource
{
    protected static ?string $model = ArticleView::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEye;

    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Article Views';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return ArticleViewsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticleViews::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
