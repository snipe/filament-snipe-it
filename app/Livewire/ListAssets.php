<?php

namespace App\Livewire;

use App\Filament\Admin\Resources\AssetResource;
use App\Models\Asset;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListAssets extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return AssetResource::table($table)
            ->query(Asset::query());
    }


    public function render(): View
    {
        return view('livewire.list-assets');
    }
}
