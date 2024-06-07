<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetModelResource\Pages;
use App\Filament\Admin\Resources\AssetModelResource\RelationManagers;
use App\Filament\Exports\AssetModelExporter;
use App\Filament\Imports\AssetModelImporter;
use App\Models\AssetModel;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetModelResource extends Resource
{
    protected static ?string $model = AssetModel::class;
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->autofocus()
                    ->maxLength(255),
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
                TextColumn::make('min_amt')->toggleable()->sortable(),
                IconColumn::make('requestable')
                    ->toggleable()
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'fas-times',
                        '1' => 'fas-check',
                    })
                    ->size(IconColumn\IconColumnSize::Small)
                    ->sortable(),
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
            ->headerActions([
                ImportAction::make()
                    ->importer(AssetModelImporter::class)->maxRows(10000),
                ExportAction::make()
                    ->exporter(AssetModelExporter::class)
                    ->fileName(fn (Export $export): string => "assetmodels-{$export->getKey()}.csv")
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
            ]);
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
            'index' => Pages\ListAssetModels::route('/'),
            'create' => Pages\CreateAssetModel::route('/create'),
            'edit' => Pages\EditAssetModel::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
