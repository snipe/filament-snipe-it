<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetResource\Pages;
use App\Filament\Admin\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Manufacturer;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'fas-barcode';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->autofocus()
                    ->maxLength(255),
                Select::make('model_id')
                    ->label('Asset Model')
                    ->options(AssetModel::select([
                        'models.id',
                        'models.name',
                        'models.image',
                        'models.model_number',
                        'models.manufacturer_id',
                        'models.category_id',
                    ])->with('manufacturer', 'category')->pluck('name', 'id'))
                    ->searchable()
                    ->native(false),
                Select::make('manufacturer_id')
                    ->label('Manufacturer')
                    ->options(Manufacturer::all()->pluck('name', 'id'))
                    ->searchable()
                    ->native(false),
                TextInput::make('email')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->maxLength(255),
                TextInput::make('jobtitle')
                    ->maxLength(255),
                FileUpload::make('image'),
                Textarea::make('notes'),
                Checkbox::make('requestable')->inline()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable()->sortable(),
                TextColumn::make('asset_tag')->sortable(),
                TextColumn::make('name')->toggleable()->sortable(),
                TextColumn::make('serial')->toggleable()->copyable()->sortable(),
                TextColumn::make('model.category.name')->toggleable()->sortable(),
                TextColumn::make('purchase_cost')->toggleable()->money('EUR', locale: 'pt')->sortable(),
                IconColumn::make('requestable')->toggleable()->boolean()->sortable(),
                TextColumn::make('purchase_date')->toggleable()->dateTime($format = 'F j, Y H:i:s')->sortable(),
                IconColumn::make('assigned_to')->toggleable()->boolean()->label('Checked Out')->sortable(),

            ])
            ->filters([
                TernaryFilter::make('Checked Out')
                    ->nullable()
                    ->attribute('assigned_to')
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->deferLoading()
            ->striped();
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
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
