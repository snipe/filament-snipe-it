<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ConsumableResource\Pages;
use App\Filament\Admin\Resources\ConsumableResource\RelationManagers;
use App\Models\Company;
use App\Models\Consumable;
use App\Models\Manufacturer;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConsumableResource extends Resource
{
    protected static ?string $model = Consumable::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';
    protected static int $globalSearchResultsLimit = 10;

    protected static ?string $navigationIcon = 'fas-tint';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Details')->schema([
                    TextInput::make('name')
                        ->required()
                        ->autofocus()
                        ->string()
                        ->maxLength(255),
                    TextInput::make('qty')
                        ->required()
                        ->numeric()
                        ->maxLength(10),
                ])
                ->id('consumable-details')
                ->columns(2),

                Section::make('Order Info')->schema([
                    Select::make('company_id')
                        ->label('Company')
                        ->options(Company::pluck('name', 'id'))
                        ->searchable()
                        ->native(false),

                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(Supplier::pluck('name', 'id'))
                        ->searchable()
                        ->native(false),

                    TextInput::make('order_number')
                        ->string()
                        ->maxLength(255),

                    TextInput::make('item_number')
                        ->string()
                        ->maxLength(255),

                    DatePicker::make('purchase_date')
                        ->suffixIcon('fas-calendar')
                        ->native(false)
                        ->displayFormat('Y-m-d'),

                    TextInput::make('purchase_cost')
                        ->string()
                        ->maxLength(255),

                    Select::make('manufacturer_id')
                        ->label('Manufacturer')
                        ->options(Manufacturer::all()->pluck('name', 'id'))
                        ->searchable()
                        ->native(false),

                    Textarea::make('notes'),
                    Toggle::make('requestable')->label('Requestable'),
                ])
                    ->collapsible()
                    ->persistCollapsed()
                    ->id('accessory-order')
                    ->columns(2)
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable()->sortable(),
                TextColumn::make('name')->toggleable()->sortable(),
                TextColumn::make('admin.username')->label('Created by')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->toggleable()
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ReplicateAction::make()->label(''),
                EditAction::make()->label(''),
                DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->persistFiltersInSession()
            ->filtersFormColumns(4)
            ->defaultPaginationPageOption(25)
            ->searchable()
            ->extremePaginationLinks()
            ->paginated([10, 25, 50, 100, 200])
            ->deferLoading()
            ->persistSortInSession()
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
            'index' => Pages\ListConsumables::route('/'),
            'create' => Pages\CreateConsumable::route('/create'),
            'edit' => Pages\EditConsumable::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

}
