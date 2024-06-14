<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SupplierResource\Pages;
use App\Filament\Clusters\Settings;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
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
use App\Filament\Exports\SupplierExporter;
use App\Filament\Imports\SupplierImporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
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
                TextInput::make('url')
                    ->url()
                    ->suffixIcon('heroicon-m-globe-alt')
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->suffixIcon('fas-envelope')
                    ->maxLength(255),
                TextInput::make('contact')
                    ->label('Contact Name')
                    ->maxLength(255),
                PhoneInput::make('phone')
                    ->initialCountry('us')
                    ->showSelectedDialCode(true),
                PhoneInput::make('fax')
                    ->initialCountry('us')
                    ->showSelectedDialCode(true),
                TextInput::make('address')
                    ->maxLength(255),
                TextInput::make('address2')
                    ->maxLength(255),
                TextInput::make('city')
                    ->maxLength(255),
                TextInput::make('state')
                    ->maxLength(255),
                TextInput::make('country')
                    ->maxLength(2),
                TextInput::make('zip')
                    ->maxLength(14),
                Textarea::make('notes')
                    ->rows(3),
                FileUpload::make('image')
                    ->directory('suppliers')
                    ->imageEditor()
                    ->image(),
            ])->columns(2);
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
                    ->importer(SupplierImporter::class)->maxRows(10000),
                ExportAction::make()
                    ->exporter(SupplierExporter::class)
                    ->fileName(fn (Export $export): string => "suppliers-{$export->getKey()}.csv")
            ])
            ->actions([
                ReplicateAction::make()
                    ->label('')
                    ->form(fn(Form $form) => SupplierResource::form($form->model(Supplier::class)))
                    ->fillForm(fn(Supplier $record) => [$record->toArray()])
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
