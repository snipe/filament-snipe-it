<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetMaintenanceResource\Pages;
use App\Filament\Clusters\Settings;
use App\Models\AssetMaintenance;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Assets;

class AssetMaintenanceResource extends Resource
{
    protected static ?string $model = AssetMaintenance::class;
    //protected static ?string $navigationGroup = 'Assets';

    protected static ?string $cluster = Assets::class;

    protected static ?string $navigationIcon = null;

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('supplier_id')
                    ->relationship(name: 'supplier', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->createOptionForm(fn(Form $form) => SupplierResource::form($form))
                    ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                        $data['user_id'] = auth()->user()->id;
                        return $data;
                    }))
                    ->required(),
                Select::make('asset_maintenance_type')
                    ->native(false)
                    ->options([
                        'preventive' => 'Preventive',
                        'corrective' => 'Corrective',
                        'predictive' => 'Predictive',
                        'condition_based' => 'Condition Based',
                    ])
                    ->required(),
                DatePicker::make('start_date')
                    ->suffixIcon('fas-calendar')
                    ->native(false)
                    ->displayFormat('Y-m-d')
                    ->required(),
                DatePicker::make('end_date')
                    ->suffixIcon('fas-calendar')
                    ->native(false)
                    ->displayFormat('Y-m-d'),
                Textarea::make('notes')->columns(1),
                Checkbox::make('is_warranty')->inline(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('asset_maintenance_type')->sortable(),
                TextColumn::make('notes')->sortable(),
                TextColumn::make('start_date')->dateTime($format = 'F j, Y')->sortable(),
                TextColumn::make('end_date')->dateTime($format = 'F j, Y')->sortable(),
                TextColumn::make('completion_date')->dateTime($format = 'F j, Y')->sortable(),
                IconColumn::make('is_warranty')
                    ->toggleable()
                    ->boolean()
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'fas-times',
                        '1' => 'fas-check',
                    })
                    ->size(IconColumn\IconColumnSize::Small)
                    ->sortable(),
            ])

//            ->filters([
//                //
//            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssetMaintenances::route('/'),
            'create' => Pages\CreateAssetMaintenance::route('/create'),
            'view' => Pages\ViewAssetMaintenance::route('/{record}'),
            'edit' => Pages\EditAssetMaintenance::route('/{record}/edit'),
        ];
    }
}
