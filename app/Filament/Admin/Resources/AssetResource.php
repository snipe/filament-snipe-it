<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetResource\Pages;
use App\Filament\Clusters\Assets;
use App\Filament\Exports\AssetExporter;
use App\Filament\Imports\AssetImporter;
//use App\Tables\Columns\ModelLinkColumn;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\ReplicateAction;
use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\StatusLabel;
use App\Models\Manufacturer;
use App\Models\Supplier;
use App\Models\Location;
use App\Models\Company;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Admin\Resources\AssetResource\RelationManagers;
use Illuminate\Contracts\Support\Htmlable;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static ?string $cluster = Assets::class;
    //protected static ?string $navigationGroup = 'Assets';
    protected static ?string $recordTitleAttribute = 'asset_tag';
    protected static int $globalSearchResultsLimit = 10;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Asset Details')->schema([

                    TextInput::make('asset_tag')
                        ->string()
                        ->required()
                        ->autofocus()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Select::make('model_id')
                        ->label('Asset Model')
                        ->relationship(name: 'assetmodel', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => AssetModelResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        }))
                        ->required(),

                    Select::make('status_id')
                        ->label('Status')
                        ->relationship(name: 'statuslabel', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => StatusLabelResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        }))
                        ->required(),

                    TextInput::make('serial')
                        ->string()
                        ->maxLength(255),

                    FileUpload::make('image')
                        ->directory('assets')
                        ->imageEditor()
                        ->image(),
                    Textarea::make('notes')
                        ->string(),
                    Checkbox::make('requestable')->inline(),
                    Checkbox::make('byod')->inline()
                ])
                ->id('asset-baseinfo')
                ->columns(2),

                Section::make('Optional Details')->schema([

                    TextInput::make('name')
                        ->maxLength(255),

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

                    Select::make('location_id')
                        ->label('Location')
                        ->relationship(name: 'location', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => LocationResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),

                    Select::make('rtd_location_id')
                        ->label('Default Location')
                        ->relationship(name: 'location', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => LocationResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),

                    DatePicker::make('expected_checkin')
                        ->suffixIcon('fas-calendar')
                        ->native(false)
                        ->displayFormat('Y-m-d'),
                    DatePicker::make('eol_date')
                        ->suffixIcon('fas-calendar')
                        ->native(false)
                        ->displayFormat('Y-m-d'),
                ])
                    ->collapsible()
                    ->persistCollapsed()
                    ->id('asset-optional')
                    ->columns(2),

                Section::make('Order Details')->schema([
                    TextInput::make('purchase_cost'),
                    TextInput::make('order_number')
                        ->string(),
                    DatePicker::make('purchase_date')
                        ->suffixIcon('fas-calendar')
                        ->native(false)
                        ->displayFormat('Y-m-d'),
                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->relationship(name: 'supplier', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => SupplierResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),
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
                ImageColumn::make('image')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('asset_tag')
                    ->sortable(),
                TextColumn::make('name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('serial')
                    ->icon('fas-copy')
                    ->toggleable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('user.username')
                    ->label('Assigned To')
                    ->toggleable()
                    ->sortable(),
                // ModelLinkColumn::make('model.manufacturer.name')->label('Manufacturer'),
                TextColumn::make('assetmodel.name')
                    ->label('Model Name')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('assetmodel.model_number')
                    ->label('Model No.')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('assetmodel.manufacturer.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('order_number')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('assetmodel.category.name')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('purchase_cost')
                    ->toggleable()
                    ->money('EUR', locale: 'pt')
                    ->sortable(),
                IconColumn::make('requestable')
                    ->toggleable()
                    ->boolean()
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'fas-times',
                        '1' => 'fas-check',
                    })
                    ->size(IconColumn\IconColumnSize::Small)
                    ->sortable(),
                TextColumn::make('purchase_date')
                    ->toggleable()
                    ->dateTime($format = 'F j, Y')
                    ->sortable(),
                IconColumn::make('assigned_to')
                    ->toggleable()
                    ->boolean()
                    ->label('Checked Out')->sortable(),
                TextColumn::make('last_audit_date')
                    ->toggleable()
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
                TextColumn::make('expected_checkin')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime($format = 'F j, Y')
                    ->sortable(),
                TextColumn::make('admin.username')->label('Created by')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
                TextColumn::make('purchase_cost')
                    ->summarize(Sum::make()->label('Total'))

            ])
            ->filters([
                Filter::make('assigned_to')
                    ->label('Checked Out')
                    ->query(fn (Builder $query): Builder => $query->whereNull('assigned_to'))
                    ->toggle(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StatusLabel::all()->pluck('name', 'id')),
                SelectFilter::make('model_id')
                    ->label('Asset Model')
                    ->options(AssetModel::all()->pluck('name', 'id')),
                SelectFilter::make('manufacturer_id')
                    ->label('Manufacturer')
                    ->options(Manufacturer::all()->pluck('name', 'id')),
                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::all()->pluck('name', 'id')),
                SelectFilter::make('location_id')
                    ->label('Location')
                    ->options(Location::all()->pluck('name', 'id')),
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->options(Company::all()->pluck('name', 'id')),
                Filter::make('requestable')
                    ->label('Requestable')
                    ->query(fn (Builder $query): Builder => $query->where('requestable', true))
                    ->toggle(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(AssetImporter::class)->maxRows(10000),
                ExportAction::make()
                    ->exporter(AssetExporter::class)
                    ->fileName(fn (Export $export): string => "assets-{$export->getKey()}.csv")
            ])
            ->actions([
                ReplicateAction::make()->label('')
                    ->excludeAttributes(
                        [
                            'assign_to',
                            'assigned_type',
                            'asset_tag'
                        ]),
                EditAction::make()->label(''),
                DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()->exporter(AssetExporter::class)
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => $record->assigned_to == null
            )
            ->deferLoading()
            ->persistSortInSession()
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MaintenancesRelationManager::class,
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


    /**
     * This is used by the global top search to determine what fields on this model we should be
     * searching on.
     *
     * @return string[]
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'assetmodel.model_number', 'serial', 'assetmodel.category.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Serial' => $record->serial,
            'Category' => $record->assetmodel->category->name,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['assetmodel', 'assetmodel.category']);
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return 'Tag #'.$record->asset_tag;
    }


//    public static function getGlobalSearchResultActions(Model $record): array
//    {
//        return [
//            Action::make('edit')
//                ->url(static::getUrl('edit', ['record' => $record])),
//        ];
//    }
}
