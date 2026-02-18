<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopArticlesTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top Articles by Views')
            ->description('Most viewed articles in the last 30 days')
            ->query(
                Article::query()
                    ->withCount(['views' => fn ($query) => $query->where('viewed_at', '>=', now()->subDays(30))])
                    ->orderByDesc('views_count')
                    ->limit(5)
            )
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn (Article $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->title) . '&background=random'),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(40)
                    ->url(fn (Article $record) => route('articles.show', $record->slug))
                    ->openUrlInNewTab(),
                TextColumn::make('category.title')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('views_count')
                    ->label('Views (30d)')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->date('M d, Y')
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
