<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\AssetMaintenance;

class MaintenancesChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Maintenances Trends';
    public ?string $filter = 'today';
    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $data = Trend::model(AssetMaintenance::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->dateColumn('start_date')
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Asset Maintenances',
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
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
}
