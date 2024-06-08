<?php

namespace App\Filament\Admin\Resources\AssetResource\Pages;

use App\Filament\Admin\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'deleted' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
