<?php

namespace App\Filament\Admin\Resources\StatusLabelResource\Pages;

use App\Filament\Admin\Resources\StatusLabelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStatusLabel extends CreateRecord
{
    protected static string $resource = StatusLabelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
