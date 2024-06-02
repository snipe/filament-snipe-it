<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\UserListing;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;

class People extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static string $view = 'filament.admin.pages.people';

    protected function getFooterWidgets(): array
    {
        return [
            UserListing::class
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }
}
