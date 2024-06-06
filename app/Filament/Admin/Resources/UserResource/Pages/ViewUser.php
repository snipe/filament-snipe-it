<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Tabs;
use Filament\Support\Enums\IconPosition;
use Filament\Resources\Concerns\HasTabs;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected static string $view = 'filament.resources.users.pages.view-user';
    use HasTabs;


    /** this doesn't work yet */
    public static function tabs() {
        return Tabs::make('Tabs')
            ->tabs([
                Tabs\Tab::make('Tab 1')
                    ->icon('heroicon-m-bell')
                    ->badge(5)
                    ->badgeColor('success')
                    ->schema([
                        // ...
                    ]),
                Tabs\Tab::make('Tab 2')
                    ->badge(12)
                    ->schema([
                        // ...
                    ]),
                Tabs\Tab::make('Tab 3')
                    ->schema([
                        // ...
                    ]),
            ])
            ->activeTab(2)
            ->persistTabInQueryString();
    }
}
