<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Tabs;
use Filament\Support\Enums\IconPosition;
use Filament\Resources\Concerns\HasTabs;
use App\Models\User;
use App\Filament\Clusters\Users;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $cluster = Users::class;

    // protected static string $view = 'filament.resources.users.pages.view-user';
    //use HasTabs;




    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

}
