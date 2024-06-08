<?php

namespace App\Filament\Admin\Resources\AssetMaintenanceResource\Pages;

use App\Filament\Admin\Resources\AssetMaintenanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAssetMaintenance extends ViewRecord
{
    protected static string $resource = AssetMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
