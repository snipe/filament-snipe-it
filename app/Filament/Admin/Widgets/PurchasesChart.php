<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Asset;

class PurchasesChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Asset Purchases Trends';
    public ?string $filter = 'today';
    protected static string $color = 'info';
    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $data = Trend::model(Asset::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->dateColumn('purchase_date')
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Asset Purchases',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
