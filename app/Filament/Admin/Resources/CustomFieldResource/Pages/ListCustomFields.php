<?php

namespace App\Filament\Admin\Resources\CustomFieldResource\Pages;

use App\Filament\Admin\Resources\CustomFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomFields extends ListRecords
{
    protected static string $resource = CustomFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
