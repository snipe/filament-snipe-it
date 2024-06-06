<?php

namespace App\Filament\Admin\Resources\ConsumableResource\Pages;

use App\Filament\Admin\Resources\ConsumableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsumables extends ListRecords
{
    protected static string $resource = ConsumableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
