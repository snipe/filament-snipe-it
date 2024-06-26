<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ManufacturerResource\Pages;
use App\Filament\Admin\Resources\ManufacturerResource\RelationManagers;
use App\Filament\Clusters\Settings;
use App\Filament\Exports\ManufacturerExporter;
use App\Filament\Imports\ManufacturerImporter;
use App\Models\Manufacturer;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
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
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Support\HtmlString;

class ManufacturerResource extends Resource
{
    protected static ?string $model = Manufacturer::class;
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
                    TextInput::make('url')
                        ->url()
                        ->suffixIcon('heroicon-m-globe-alt')
                        ->maxLength(255),
                    TextInput::make('support_url')
                        ->url()
                        ->suffixIcon('heroicon-m-globe-alt')
                        ->maxLength(255),
                    TextInput::make('warranty_lookup_url')
                        ->url()
                        ->suffixIcon('heroicon-m-globe-alt')
                        ->helperText(str('Variables `{LOCALE}`, `{SERIAL}`, `{MODEL_NUMBER}`, and `{MODEL_NAME}` may be used in your URL to have those values auto-populate when viewing assets - for example https://checkcoverage.apple.com/{LOCALE}/`{SERIAL}`.')->inlineMarkdown()->toHtmlString())
                        ->maxLength(255),
                    TextInput::make('support_email')
                        ->email()
                        ->suffixIcon('fas-envelope')
                        ->maxLength(255),
                    PhoneInput::make('support_phone')
                        ->showSelectedDialCode(true),
                    FileUpload::make('image')
                        ->directory('manufacturers')
                        ->imageEditor()
                        ->image(),
                    Textarea::make('notes')
                        ->string(),
                ])
                ->id('manufacturer-basic')
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
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('name')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('notes')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
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
                    ->importer(ManufacturerImporter::class)->maxRows(10000),
                ExportAction::make()
                    ->exporter(ManufacturerExporter::class)
                    ->fileName(fn (Export $export): string => "manufacturers-{$export->getKey()}.csv")
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
            'index' => Pages\ListManufacturers::route('/'),
            'create' => Pages\CreateManufacturer::route('/create'),
            'edit' => Pages\EditManufacturer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
