<?php

namespace App\Livewire;

use App\Filament\Admin\Resources\LicenseResource;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Livewire\Component;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ListLicenses extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $model = User::class;
    public User $record;


    /**
     * This gets the definition for assets, using the AssetResource::table().
     * Using the public variable $record, we can get the assets for just the selected user.
     *
     * We get the assets for the user by user relationship from User->assets().
     *
     * @see App/Filament/Admin/Resources/AssetResource::table()
     *
     * @param Table $table
     * @return Table
     */
    public function table(Table $table)
    {
        return LicenseResource::table($table)->relationship(fn (): BelongsToMany => $this->record->licenses());

    }


    /**
     * This renders the view for the assets list.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.list-licenses');
    }
}
