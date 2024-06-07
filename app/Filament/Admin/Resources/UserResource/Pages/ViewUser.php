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

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $cluster = Users::class;

    // protected static string $view = 'filament.resources.users.pages.view-user';
    use HasTabs;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }


    public function getTabs(): array
    {
        $tabs = ['all' => Tabs::make('All')->badge($this->getModel()::count())];

        $users = User::orderBy('id', 'asc')
            ->withCount('assets')
            ->withCount('accessories')
            ->get();

        foreach ($users as $user) {
            $name = $user->name;
            $slug = str($name)->slug()->toString();

            $tabs[$slug] = Tabs::make($name)
                ->badge($user->assets)
                ->modifyQueryUsing(function ($query) use ($user) {
                    return $query->where('id', $user->id);
                });
        }

        return $tabs;
    }
}
