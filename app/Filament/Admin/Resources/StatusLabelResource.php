<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StatusLabelResource\Pages;
use App\Filament\Admin\Resources\StatusLabelResource\RelationManagers;
use App\Filament\Clusters\Settings;
use App\Models\StatusLabel;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ColorColumn;

class StatusLabelResource extends Resource
{
    protected static ?string $model = StatusLabel::class;
    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->string()
                    ->required()
                    ->autofocus()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                ColorPicker::make('color'),
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
                Textarea::make('notes')
                    ->columnSpan(2),
                Checkbox::make('default_label')
                    ->inline()->columnSpan(2)
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable()->sortable(),
                TextColumn::make('name')->toggleable()->sortable(),
                TextColumn::make('notes')->toggleable()->sortable(),
                TextColumn::make('created_at')
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->toggleable()
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
                ColorColumn::make('color'),
                TextColumn::make('admin.username')->label('Created by')
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ReplicateAction::make()
                    ->label('')
                    ->excludeAttributes(
                        ['name']),
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
            'index' => Pages\ListStatusLabels::route('/'),
            'create' => Pages\CreateStatusLabel::route('/create'),
            'edit' => Pages\EditStatusLabel::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
