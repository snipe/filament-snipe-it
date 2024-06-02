<?php

namespace App\Filament\Admin\Resources\AccessoryResource\Pages;

use App\Filament\Admin\Resources\AccessoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccessories extends ListRecords
{
    protected static string $resource = AccessoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
