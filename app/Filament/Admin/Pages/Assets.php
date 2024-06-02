<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Filament\Admin\Widgets\AssetListing;

class Assets extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static string $view = 'filament.admin.pages.assets';

    protected function getFooterWidgets(): array
    {
        return [
            AssetListing::class
        ];
    }
}
