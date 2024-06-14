<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LocationResource\Pages;
use App\Filament\Admin\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Exports\LocationExporter;
use App\Filament\Imports\LocationImporter;
use App\Filament\Clusters\Settings;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;


class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    TextInput::make('name')
                        ->string()
                        ->required()
                        ->autofocus()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Select::make('parent_id')
                        ->relationship(name: 'parent', titleAttribute: 'name', ignoreRecord: true)
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => LocationResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),
                    Select::make('manager_id')
                        ->relationship(name: 'manager', titleAttribute: 'first_name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => UserResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),

                    TextInput::make('ldap_ou')
                        ->maxLength(255),
                    TextInput::make('address')
                        ->maxLength(255),
                    TextInput::make('address2')
                        ->maxLength(255),
                    TextInput::make('city')
                        ->maxLength(255),
                    TextInput::make('state')
                        ->maxLength(255),
                    TextInput::make('country')
                        ->maxLength(2),
                    TextInput::make('zip')
                        ->maxLength(14),
                    PhoneInput::make('phone')
                        ->showSelectedDialCode(true),
                    PhoneInput::make('fax')
                        ->showSelectedDialCode(true),
                    FileUpload::make('image')
                        ->directory('locations')
                        ->imageEditor()
                        ->image(),
                ])->columns(2)
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
                    ->sortable(),
                PhoneColumn::make('phone')
                    ->displayFormat(PhoneInputNumberType::NATIONAL)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('fas-square-phone')
                    ->url(fn ($record) => 'tel:'.$record->phone, true)
                    ->sortable(),
                PhoneColumn::make('fax')
                    ->displayFormat(PhoneInputNumberType::NATIONAL)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->url(fn ($record) => 'tel:'.$record->fax, true)
                    ->icon('fas-fax')
                    ->sortable(),
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
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(LocationImporter::class)->maxRows(10000),
                ExportAction::make()
                    ->exporter(LocationExporter::class)
                    ->fileName(fn (Export $export): string => "locations-{$export->getKey()}.csv")
            ])
            ->actions([
                ReplicateAction::make()
                    ->label('')
                    ->excludeAttributes(
                        [
                            'name',
                        ]),
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
