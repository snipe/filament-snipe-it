<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ComponentResource\Pages;
use App\Filament\Admin\Resources\ComponentResource\RelationManagers;
use App\Models\Category;
use App\Models\Component;
use App\Models\Manufacturer;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ComponentResource extends Resource
{
    protected static ?string $model = Component::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';
    protected static int $globalSearchResultsLimit = 10;

    protected static ?string $navigationIcon = 'far-hdd';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Component Details')->schema([
                    TextInput::make('name')
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->autofocus()
                        ->string()
                        ->maxLength(255),

                    Select::make('category_id')
                        ->options(Category::where('category_type','component')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => CategoryResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),

                    TextInput::make('qty')
                        ->numeric()
                        ->required()
                        ->maxLength(10),
                    TextInput::make('serial')
                        ->string()
                        ->maxLength(255),
                    TextInput::make('model_number')
                        ->string()
                        ->maxLength(255),
                    TextInput::make('min_amt')
                        ->numeric()
                        ->maxLength(255)
                        ->helperText('This is the minimum amount of this component that should be kept in stock.'),
                    ])
                    ->id('component-details')
                    ->columns(2),
                Section::make('Order Details')->schema([

                    Select::make('company_id')
                        ->label('Company')
                        ->relationship(name: 'company', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => CompanyResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),

                    TextInput::make('order_number')
                        ->string()
                        ->maxLength(255),
                    TextInput::make('purchase_cost'),
                    DatePicker::make('purchase_date')
                        ->suffixIcon('fas-calendar')
                        ->native(false)
                        ->displayFormat('Y-m-d'),
                    Select::make('manufacturer_id')
                        ->relationship(name: 'manufacturer', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => ManufacturerResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),
                    Select::make('location_id')
                        ->relationship(name: 'location', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => LocationResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),
                    Select::make('supplier_id')
                        ->relationship(name: 'supplier', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => SupplierResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),
                    Textarea::make('notes')
                        ->string(),
                    FileUpload::make('image')
                        ->directory('components')
                        ->imageEditor()
                        ->image()
                    ])
                    ->collapsed()
                    ->persistCollapsed()
                    ->id('optional-details')
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                ImageColumn::make('image')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('name')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serial')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('admin.username')
                    ->label('Created by')
                    ->searchable()
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
            'index' => Pages\ListComponents::route('/'),
            'create' => Pages\CreateComponent::route('/create'),
            'edit' => Pages\EditComponent::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * This is used by the global top search to determine what fields on this model we should be
     * searching on.
     *
     * @return string[]
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'admin.username'];
    }
}
