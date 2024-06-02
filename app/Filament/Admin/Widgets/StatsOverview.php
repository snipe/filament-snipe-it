<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Asset;
use App\Models\User;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Assets', number_format(Asset::count()))
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('People', number_format(User::count()))
                ->description('7% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 7, 10, 36, 15, 4, 50])
                ->color('success'),
            Stat::make('Some other stat', '3:12')
                ->description('3% decrease')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([70, 7, 10, 36, 15, 4, 17])
                ->color('danger'),
        ];
    }


}
