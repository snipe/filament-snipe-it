<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Asset;

class CheckoutsChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Checkout Trends';
    public ?string $filter = 'today';
    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $data = Trend::model(Asset::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->dateColumn('last_checkout')
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Asset Checkouts',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
}
