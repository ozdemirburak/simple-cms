<?php

namespace App\Filament\Widgets;

use App\Models\ArticleView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ArticleViewsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Article Views';

    protected ?string $description = 'Daily views over the last 30 days';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = collect(range(29, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'date' => $date->format('M d'),
                'count' => ArticleView::whereDate('viewed_at', $date)->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $data->pluck('count')->toArray(),
                    'fill' => true,
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                    'borderColor' => 'rgb(251, 191, 36)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }
}
