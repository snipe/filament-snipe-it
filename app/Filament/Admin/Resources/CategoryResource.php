<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Filament\Admin\Resources\CategoryResource\RelationManagers;
use App\Filament\Clusters\Settings;
use App\Filament\Exports\CategoryExporter;
use App\Filament\Imports\CategoryImporter;
use App\Models\Category;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MarkdownEditor;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
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
                    MarkdownEditor::make('eula_text')
                        ->disableToolbarButtons([
                            'attachFiles',
                            'strike',
                        ]),
                    ToggleButtons::make('category_type')
                        ->options([
                            'asset' => 'Asset',
                            'accessory' => 'Accessory',
                            'license' => 'License',
                            'consumable' => 'Consumable',
                            'component' => 'Component',
                        ])
                        ->icons([
                            'asset' => 'fas-barcode',
                            'accessory' => 'far-keyboard',
                            'license' => 'far-save',
                            'consumable' => 'fas-tint',
                            'component' => 'far-hdd',
                        ])
                        ->required()
                        ->grouped()
                        ->inline(),
                    Toggle::make('use_default_eula')
                        ->onIcon('fas-check-circle')
                        ->offIcon('fas-times-circle')
                        ->onColor('success')
                        ->offColor('gray'),
                    Toggle::make('require_acceptance')
                        ->onIcon('fas-check-circle')
                        ->offIcon('fas-times-circle'),
                    Toggle::make('checkin_email')
                        ->onIcon('fas-envelope-circle-check')
                        ->offIcon('fas-envelope')
                        ->onColor('success')
                        ->offColor('gray'),
                    FileUpload::make('image')
                        ->directory('categories')
                        ->imageEditor()
                        ->image(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->toggleable()
                    ->sortable(),
                ImageColumn::make('image')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('name')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('category_type')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('admin.username')->label('Created by')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('eula_text')
                    ->markdown()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->words(10)
                    ->toggleable()
                    ->sortable(),
                ToggleColumn::make('use_default_eula')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                ToggleColumn::make('checkin_email')
                    ->toggleable(isToggledHiddenByDefault: true)
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
            ->headerActions([
                ImportAction::make()
                    ->importer(CategoryImporter::class)->maxRows(10000),
                ExportAction::make()
                    ->exporter(CategoryExporter::class)
                    ->fileName(fn (Export $export): string => "categories-{$export->getKey()}.csv")
            ])
            ->actions([
                ReplicateAction::make()->label('')
                    ->excludeAttributes(
                    [
                        'name',
                    ]),
                EditAction::make()->label(''),
                DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
