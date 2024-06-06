<?php

namespace App\Filament\Admin\Resources\CustomFieldsetResource\Pages;

use App\Filament\Admin\Resources\CustomFieldsetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomFieldsets extends ListRecords
{
    protected static string $resource = CustomFieldsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
