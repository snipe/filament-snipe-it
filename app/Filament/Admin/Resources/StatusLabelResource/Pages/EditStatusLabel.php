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

    protected function mutateFormDataBeforeFill(array $data): array
    {

        if ($data['deployable'] == 1) {
            $data['status_type'] = 'deployable';
        } elseif ($data['pending'] == 1) {
            $data['status_type'] = 'pending';
        } elseif ($data['archived'] == 1) {
            $data['status_type'] = 'archived';
        }

        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        switch ($data['status_type']) {

            case 'deployable':
                $data['deployable'] = 1;
                $data['pending'] = 0;
                $data['archived'] = 0;
                break;
            case 'archived':
                $data['deployable'] = 0;
                $data['pending'] = 0;
                $data['archived'] = 1;
                break;
            case 'pending':
                $data['deployable'] = 0;
                $data['pending'] = 1;
                $data['archived'] = 0;
                break;

            default:
                $data['deployable'] = 0;
                $data['pending'] = 0;
                $data['archived'] = 1;
                break;
        }

        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
