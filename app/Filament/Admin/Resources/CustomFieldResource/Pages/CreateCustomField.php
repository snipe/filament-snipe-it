<?php

namespace App\Filament\Admin\Resources\CustomFieldResource\Pages;

use App\Filament\Admin\Resources\CustomFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomField extends CreateRecord
{
    protected static string $resource = CustomFieldResource::class;
}
