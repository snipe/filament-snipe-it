<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CustomFieldsetResource\Pages;
use App\Filament\Admin\Resources\CustomFieldsetResource\RelationManagers;
use App\Models\CustomFieldset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomFieldsetResource extends Resource
{
    protected static ?string $model = CustomFieldset::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable()->sortable(),
                TextColumn::make('name')->toggleable()->sortable(),
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
            'index' => Pages\ListCustomFieldsets::route('/'),
            'create' => Pages\CreateCustomFieldset::route('/create'),
            'edit' => Pages\EditCustomFieldset::route('/{record}/edit'),
        ];
    }
}
