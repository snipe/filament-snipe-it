<?php

namespace App\Filament\Admin\Resources\AssetModelResource\Pages;

use App\Filament\Admin\Resources\AssetModelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetModel extends CreateRecord
{
    protected static string $resource = AssetModelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
