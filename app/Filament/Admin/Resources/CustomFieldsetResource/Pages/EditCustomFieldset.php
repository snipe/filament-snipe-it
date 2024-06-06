<?php

namespace App\Filament\Admin\Resources\CustomFieldsetResource\Pages;

use App\Filament\Admin\Resources\CustomFieldsetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomFieldset extends EditRecord
{
    protected static string $resource = CustomFieldsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
