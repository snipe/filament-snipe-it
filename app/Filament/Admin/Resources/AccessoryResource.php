<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AccessoryResource\Pages;
use App\Filament\Admin\Resources\AccessoryResource\RelationManagers;
use App\Forms\Components\ViewImage;
use App\Models\Accessory;
use App\Models\Location;
use App\Models\Company;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use App\Models\Category;
use App\Models\Manufacturer;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\HtmlString;

class AccessoryResource extends Resource
{
    protected static ?string $model = Accessory::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'far-keyboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Details')->schema([
                    TextInput::make('name')
                        ->string()
                        ->required()
                        ->autofocus()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Select::make('category_id')
                        ->label('Category')
                        ->options(Category::where('category_type','accessory')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->native(false),

                    TextInput::make('qty')
                        ->numeric()
                        ->required()
                        ->autofocus()
                        ->maxLength(10),

                    TextInput::make('min_amt')
                        ->numeric()
                        ->maxLength(10),

                    Select::make('location_id')
                        ->label('Location')
                        ->options(Location::pluck('name', 'id'))
                        ->searchable()
                        ->native(false),

                    FileUpload::make('image')
                        ->directory('accessories')
                        ->imageEditor()
                        ->image(),
                ])
                ->id('accessory-details')
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

                    TextInput::make('model_number')
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
                ImageColumn::make('image')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('name')->toggleable()->sortable(),
                TextColumn::make('model_number')->toggleable()->sortable(),
                TextColumn::make('category.name')->toggleable()->sortable(),
                TextColumn::make('qty')->toggleable()->sortable(),
                TextColumn::make('min_amt')->toggleable()->sortable(),
                TextColumn::make('admin.username')->toggleable()->sortable(),
                TextColumn::make('purchase_cost')->toggleable()->money('EUR', locale: 'pt')->sortable(),
                TextColumn::make('purchase_date')->toggleable()->dateTime($format = 'F j, Y')->sortable(),
                TextColumn::make('order_number')->toggleable()->sortable(),
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

                // this creates the checkbox filter for the category
                Tables\Filters\Filter::make('type')
                    ->columnSpanFull()
                    ->form([
                        Forms\Components\CheckboxList::make('category')
                            ->label('')
                            ->columns(5)
                            ->options(function (): array {
                                return Category::where('category_type', 'accessory')
                                    ->withCount('accessories as accessory_count')
                                    ->get()
                                    ->mapWithKeys(function (Category $category) {
                                        return [
                                            $category->id => new HtmlString($category->name . " <span class='text-gray-500'>({$category->accessory_count})</span>")
                                        ];
                                    })
                                    ->all();
                            }),
                    ])
                    // and this creates the callback to filter the results
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['category'], fn (Builder $query) => $query->whereIn('category_id', $data['category']));
                    }),
            ], Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                EditAction::make()->label(''),
                DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
