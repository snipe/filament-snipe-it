<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetResource\Pages;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
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

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'fas-barcode';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('asset_tag')
                    ->autofocus()
                    ->maxLength(255)
                    ->required(),
                TextInput::make('name')
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
                    ->native(false)
                    ->required(),
                Select::make('status_id')
                    ->label('Status')
                    ->relationship(name: 'statuslabel', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                        ToggleButtons::make('status_type')
                            ->options([
                                'deployable' => 'Deployable',
                                'pending' => 'Pending',
                                'undeployable' => 'Undeployable',
                                'archived' => 'Archived'
                            ])
                            ->colors([
                                'deployable' => 'success',
                                'pending' => 'primary',
                                'undeployable' => 'primary',
                                'archived' => 'danger'
                            ])
                            ->icons([
                                'deployable' => 'fas-check',
                                'pending' => 'heroicon-o-clock',
                                'undeployable' => 'fas-times',
                                'archived' => 'fas-times',
                            ])
                            ->required()
                            ->grouped()
                            ->inline(),
                        ColorPicker::make('color')
                    ])
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
                    ->native(false)
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                    ]),
                Select::make('rtd_location_id')
                    ->label('Default Location')
                    ->options(Location::all()->pluck('name', 'id'))
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                    ])
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
                TextColumn::make('company.name')->toggleable()->sortable(),
                TextColumn::make('assigned_to')->toggleable()->sortable(),
                // ModelLinkColumn::make('model.manufacturer.name')->label('Manufacturer'),
                TextColumn::make('model.name')->label('Model Name')->toggleable()->sortable(),
                TextColumn::make('model.model_number')->label('Model No.')->toggleable()->sortable(),
                TextColumn::make('model.manufacturer.name')->toggleable()->sortable(),
                TextColumn::make('order_number')->toggleable()->sortable(),
                TextColumn::make('model.category.name')->toggleable()->sortable(),
                TextColumn::make('purchase_cost')->toggleable()->money('EUR', locale: 'pt')->sortable(),
                IconColumn::make('requestable')
                    ->toggleable()
                    ->boolean()
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'fas-times',
                        '1' => 'fas-check',
                    })
                    ->size(IconColumn\IconColumnSize::Small)
                    ->sortable(),
                TextColumn::make('purchase_date')->toggleable()->dateTime($format = 'F j, Y')->sortable(),
                IconColumn::make('assigned_to')->toggleable()->boolean()->label('Checked Out')->sortable(),
                TextColumn::make('last_audit_date')->toggleable()->dateTime($format = 'F j, Y H:i:s')->sortable(),
                TextColumn::make('expected_checkin')->toggleable()->dateTime($format = 'F j, Y')->sortable(),
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
                TernaryFilter::make('Checked Out')
                    ->nullable()
                    ->attribute('assigned_to'),
                SelectFilter::make('status')
                    ->options(StatusLabel::all()->pluck('name', 'id')),
                SelectFilter::make('model_id')->label('Asset Model')
                    ->options(AssetModel::all()->pluck('name', 'id')),
                SelectFilter::make('manufacturer_id')
                    ->label('Manufacturer')
                    ->options(Manufacturer::all()->pluck('name', 'id')),
                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::all()->pluck('name', 'id')),
                SelectFilter::make('Location_id')
                    ->options(Location::all()->pluck('name', 'id')),
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->options(Company::all()->pluck('name', 'id')),
                TernaryFilter::make('Requestable')
                    ->attribute('requestable'),
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
