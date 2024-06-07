<?php

namespace App\Filament\Admin\Resources\AssetModelResource\Pages;

use App\Filament\Admin\Resources\AssetModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetModel extends EditRecord
{
    protected static string $resource = AssetModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
