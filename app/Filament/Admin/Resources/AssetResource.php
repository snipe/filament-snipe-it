<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetResource\Pages;
use App\Filament\Admin\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\StatusLabel;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Supplier;
use App\Models\Location;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'fas-barcode';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->maxLength(255)
                    ->required(),
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
                    ->native(false)
                    ->required(),
                Select::make('status_id')
                    ->label('Status')
                    ->options(StatusLabel::all()->pluck('name', 'id'))
                    ->searchable()
                    ->native(false)
                    ->required(),
                Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::all()->pluck('name', 'id'))
                    ->searchable()
                    ->native(false),
                Select::make('location_id')
                    ->label('Location')
                    ->options(Location::all()->pluck('name', 'id'))
                    ->searchable()
                    ->native(false),
                Select::make('rtd_location_id')
                    ->label('Default Location')
                    ->options(Location::all()->pluck('name', 'id'))
                    ->searchable()
                    ->native(false),
                DatePicker::make('purchase_date')
                    ->format('Y-m-d'),
                DatePicker::make('expected_checkin')
                    ->format('Y-m-d'),
                DatePicker::make('eol_date')
                    ->format('Y-m-d'),
                FileUpload::make('image'),
                TextInput::make('purchase_cost'),
                TextInput::make('order_number'),
                Textarea::make('notes'),
                Checkbox::make('requestable')->inline(),
                Checkbox::make('byod')->inline()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable()->sortable(),
                ImageColumn::make('image')->sortable(),
                TextColumn::make('asset_tag')->sortable(),
                TextColumn::make('name')->toggleable()->sortable(),
                TextColumn::make('serial')->toggleable()->copyable()->sortable(),
                TextColumn::make('assigned_to')->toggleable()->sortable(),
                TextColumn::make('model.manufacturer.name')->toggleable()->sortable(),
                TextColumn::make('model.model_number')->toggleable()->sortable(),
                TextColumn::make('order_number')->toggleable()->sortable(),
                TextColumn::make('model.category.name')->toggleable()->sortable(),
                TextColumn::make('model.name')->toggleable()->sortable(),
                TextColumn::make('purchase_cost')->toggleable()->money('EUR', locale: 'pt')->sortable(),
                IconColumn::make('requestable')->toggleable()->boolean()->sortable(),
                TextColumn::make('purchase_date')->toggleable()->dateTime($format = 'F j, Y H:i:s')->sortable(),
                IconColumn::make('assigned_to')->toggleable()->boolean()->label('Checked Out')->sortable(),

            ])
            ->filters([
                TernaryFilter::make('Checked Out')
                    ->nullable()
                    ->attribute('assigned_to'),
                SelectFilter::make('status')
                    ->options(StatusLabel::all()->pluck('name', 'id')),
                SelectFilter::make('model_id')->label('Asset Model')
                    ->options(AssetModel::all()->pluck('name', 'id')),
                SelectFilter::make('manufacturer')
                    ->options(Manufacturer::all()->pluck('name', 'id')),
                SelectFilter::make('Supplier')
                    ->options(Supplier::all()->pluck('name', 'id')),
                SelectFilter::make('Location')
                    ->options(Location::all()->pluck('name', 'id')),
                TernaryFilter::make('Requestable')
                    ->attribute('requestable'),

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
