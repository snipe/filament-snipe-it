<?php

namespace App\Filament\Admin\Resources\ConsumableResource\Pages;

use App\Filament\Admin\Resources\ConsumableResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateConsumable extends CreateRecord
{
    protected static string $resource = ConsumableResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
