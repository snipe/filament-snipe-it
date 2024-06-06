<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetModelResource\Pages;
use App\Filament\Admin\Resources\AssetModelResource\RelationManagers;
use App\Models\AssetModel;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetModelResource extends Resource
{
    protected static ?string $model = AssetModel::class;
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                ToggleColumn::make('requestable')->toggleable(isToggledHiddenByDefault: true)->sortable(),
                TextColumn::make('updated_at')->toggleable()->dateTime($format = 'F j, Y H:i:s')->sortable(),
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
}
