<?php

namespace App\Livewire;

use App\Filament\Admin\Resources\AssetResource;
use App\Filament\Admin\Resources\UserResource;
use App\Models\Asset;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ListAssets extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $model = Asset::class;

    public function table(Table $table): Table
    {
        return AssetResource::table($table)
            ->query(Asset::query())
            ->inverseRelationship('user');
//            ->relationship(fn (): HasMany => $this->assets)
//            ->inverseRelationship('categories');
    }


    public function render(): View
    {
        return view('livewire.list-assets');
    }
}
