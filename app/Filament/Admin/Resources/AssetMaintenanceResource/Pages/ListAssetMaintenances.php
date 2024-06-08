<?php

namespace App\Filament\Admin\Resources\AssetMaintenanceResource\Pages;

use App\Filament\Admin\Resources\AssetMaintenanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssetMaintenances extends ListRecords
{
    protected static string $resource = AssetMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
