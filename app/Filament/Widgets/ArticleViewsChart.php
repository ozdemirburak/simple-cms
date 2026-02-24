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
        $startDate = Carbon::today()->subDays(29);

        $viewsByDate = ArticleView::where('viewed_at', '>=', $startDate)
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(viewed_at)')
            ->pluck('count', 'date');

        $data = collect(range(29, 0))->map(function ($daysAgo) use ($viewsByDate) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'date' => $date->format('M d'),
                'count' => $viewsByDate->get($date->toDateString(), 0),
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
