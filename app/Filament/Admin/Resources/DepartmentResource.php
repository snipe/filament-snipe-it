<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DepartmentResource\Pages;
use App\Filament\Admin\Resources\DepartmentResource\RelationManagers;
use App\Filament\Clusters\Settings;
use App\Models\Department;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
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
use App\Filament\Exports\DepartmentExporter;
use App\Filament\Imports\DepartmentImporter;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;
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
                FileUpload::make('image')
                    ->directory('departments')
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
                    ->toggleable(isToggledHiddenByDefault: true)
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
            ->headerActions([
                ImportAction::make()
                    ->importer(DepartmentImporter::class)->maxRows(10000),
                ExportAction::make()
                    ->exporter(DepartmentExporter::class)
                    ->fileName(fn (Export $export): string => "departments-{$export->getKey()}.csv")
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

}
