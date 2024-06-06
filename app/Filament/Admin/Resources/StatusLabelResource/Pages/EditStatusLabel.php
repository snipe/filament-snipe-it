<?php

namespace App\Filament\Admin\Resources\StatusLabelResource\Pages;

use App\Filament\Admin\Resources\StatusLabelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatusLabel extends EditRecord
{
    protected static string $resource = StatusLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
