<?php

namespace App\Filament\Admin\Resources\AssetResource\RelationManagers;

use App\Filament\Admin\Resources\SupplierResource;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\AssetMaintenanceResource as AssetMaintenanceResource;

class MaintenancesRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenances';

    public function form(Form $form): Form
    {
        return AssetMaintenanceResource::form($form);
    }

    /**
     * This pulls in the AssetMaintenanceResource table that is scoped to that asset.
     * When this is invoked, it will show the table of maintenances for that asset,
     * and any new maintenances made from a page will be associated with that asset.
     *
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return AssetMaintenanceResource::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make()
            ]);
    }

}
