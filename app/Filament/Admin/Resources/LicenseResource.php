<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LicenseResource\Pages;
use App\Filament\Admin\Resources\LicenseResource\RelationManagers;
use App\Models\Category;
use App\Models\Company;
use App\Models\License;
use App\Models\Manufacturer;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $navigationIcon = 'far-save';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Asset Details')->schema([

                    TextInput::make('name')
                        ->string()
                        ->required()
                        ->autofocus()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Select::make('category_id')
                        ->options(Category::where('category_type','license')->pluck('name', 'id'))
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
                        ->label('Seats')
                        ->numeric()
                        ->required()
                        ->maxLength(10),

                    TextInput::make('min_qty')
                        ->numeric()
                        ->extraInputAttributes([
                            'min' => 1,
                            'max' => 9999,
                        ])
                        ->maxLength(10),

                    TextInput::make('product_key')
                        ->string()
                        ->maxLength(255),

                    Select::make('company_id')
                        ->label('Company')
                        ->options(Company::all()->pluck('name', 'id'))
                        ->searchable()
                        ->native(false),

                    Textarea::make('notes')
                        ->string()
                    ])
                    ->id('license-baseinfo')
                    ->columns(2),

                Section::make('Optional Details')->schema([
                    TextInput::make('order_number')
                        ->string()
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

                    TextInput::make('purchase_cost'),
                    DatePicker::make('purchase_date')
                        ->suffixIcon('fas-calendar')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                    ])
                    ->collapsed()
                    ->persistCollapsed()
                    ->id('order-details')
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
                TextColumn::make('name')
                    ->toggleable()->sortable(),
                TextColumn::make('serial')
                    ->toggleable()->sortable(),
                TextColumn::make('admin.username')
                    ->label('Created by')
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
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'edit' => Pages\EditLicense::route('/{record}/edit'),
        ];
    }
}
