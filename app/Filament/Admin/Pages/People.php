<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\UserListing;
use Filament\Pages\Page;

class People extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.people';

    protected function getFooterWidgets(): array
    {
        return [
            UserListing::class
        ];
    }
}
