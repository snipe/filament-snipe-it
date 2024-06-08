<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ComponentResource\Pages;
use App\Filament\Admin\Resources\ComponentResource\RelationManagers;
use App\Models\Component;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
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
                TextInput::make('name')
                    ->unique()
                    ->required()
                    ->autofocus()
                    ->string()
                    ->maxLength(255),
                FileUpload::make('image')
                    ->directory('components')
                    ->imageEditor()
                    ->image(),
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
