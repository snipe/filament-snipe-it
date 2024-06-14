<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DepartmentResource\Pages;
use App\Filament\Admin\Resources\DepartmentResource\RelationManagers;
use App\Filament\Clusters\Settings;
use App\Models\Company;
use App\Models\Department;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
use App\Filament\Exports\DepartmentExporter;
use App\Filament\Imports\DepartmentImporter;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;
    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Component Details')->schema([
                    TextInput::make('name')
                        ->string()
                        ->required()
                        ->autofocus()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    PhoneInput::make('phone')
                        ->showSelectedDialCode(true),
                    PhoneInput::make('fax')
                        ->showSelectedDialCode(true),
                    Select::make('company_id')
                        ->relationship(name: 'company', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => CompanyResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),
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
                    Select::make('location_id')
                        ->relationship(name: 'location', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => LocationResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),
                    Textarea::make('notes')
                        ->string()
                        ->nullable(),
                    FileUpload::make('image')
                        ->directory('departments')
                        ->imageEditor()
                        ->image(),
                  ])
                ->id('optional-details')
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
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('name')
                    ->toggleable()
                    ->sortable(),
                PhoneColumn::make('phone')
                    ->displayFormat(PhoneInputNumberType::NATIONAL)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('fas-square-phone')
                    ->url(fn ($record) => 'tel:'.$record->phone, true)
                    ->sortable(),
                PhoneColumn::make('fax')
                    ->displayFormat(PhoneInputNumberType::NATIONAL)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->url(fn ($record) => 'tel:'.$record->fax, true)
                    ->icon('fas-fax')
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
