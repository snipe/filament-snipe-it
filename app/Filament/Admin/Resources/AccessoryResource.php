<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AccessoryResource\Pages;
use App\Filament\Admin\Resources\AccessoryResource\RelationManagers;
use App\Models\Accessory;
use Filament\Forms;
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
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Widgets\StatsOverview;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use App\Models\Category;
use App\Models\Manufacturer;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;

class AccessoryResource extends Resource
{
    protected static ?string $model = Accessory::class;
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'far-keyboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->autofocus()
                    ->maxLength(255),
                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('category_type','accessory')->pluck('name', 'id'))
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
                TextColumn::make('name')->toggleable()->sortable(),
                TextColumn::make('model_number')->toggleable()->sortable(),
                TextColumn::make('category.name')->toggleable()->sortable(),
                TextColumn::make('qty')->toggleable()->sortable(),
                TextColumn::make('min_amt')->toggleable()->sortable(),
                TextColumn::make('admin')->toggleable()->sortable(),
                TextColumn::make('purchase_cost')->toggleable()->money('EUR', locale: 'pt')->sortable(),
                TextColumn::make('purchase_date')->toggleable()->dateTime($format = 'F j, Y')->sortable(),
                TextColumn::make('order_number')->toggleable()->sortable(),
                ToggleColumn::make('requestable')->toggleable(isToggledHiddenByDefault: true)->sortable(),
                TextColumn::make('updated_at')->toggleable()->dateTime($format = 'F j, Y H:i:s')->sortable(),
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
            'index' => Pages\ListAccessories::route('/'),
            'create' => Pages\CreateAccessory::route('/create'),
            'edit' => Pages\EditAccessory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }



}
