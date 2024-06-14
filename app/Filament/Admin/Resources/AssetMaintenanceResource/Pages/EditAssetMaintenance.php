<?php

namespace App\Filament\Admin\Resources\AssetMaintenanceResource\Pages;

use App\Filament\Admin\Resources\AssetMaintenanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetMaintenance extends EditRecord
{
    protected static string $resource = AssetMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
