<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Location;
use App\Models\Department;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\UserExporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Imports\UserImporter;
use Filament\Tables\Actions\ImportAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([

                Section::make('Name and Login')->schema([
                    TextInput::make('first_name')
                        ->maxLength(255)
                        ->required()
                        ->autofocus(),
                    TextInput::make('last_name')
                        ->maxLength(255),
                    TextInput::make('username')
                        ->maxLength(255)
                        ->unique()
                        ->required(),
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create'),
                    Checkbox::make('activated')->label('This user can login')->inline()
                ])
                ->columns(2),

                Section::make('Work Information')->schema([
                    TextInput::make('jobtitle')
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Select::make('location_id')
                        ->relationship(name: 'location', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required()
                        ]),
                    Select::make('department_id')
                        ->relationship(name: 'department', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->createOptionForm(fn(Form $form) => DepartmentResource::form($form))
                        ->createOptionAction(fn ($action) => $action->mutateFormDataUsing(function ($data) {
                            $data['user_id'] = auth()->user()->id;
                            return $data;
                        })),
                    ])
                    ->collapsible()
                    ->persistCollapsed()
                    ->id('user-workinfo')
                    ->columns(2),
                Section::make('Address')
                    //->description('Settings for publishing this post.')
                    ->schema([
                    TextInput::make('address')
                        ->maxLength(255),
                    TextInput::make('address2')->label('Address Line 2')
                        ->maxLength(255),
                    TextInput::make('city')
                        ->maxLength(255),
                    TextInput::make('state')
                        ->maxLength(255),
                    TextInput::make('zip')
                        ->maxLength(14),
                    TextInput::make('country')
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->maxLength(255)
                    ])
                    ->collapsed()
                    ->persistCollapsed()
                    ->id('user-address')
                    ->columns(2),
                    //->suffixIcon('heroicon-m-globe-alt'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                ImageColumn::make('avatar')
                    ->toggleable()
                    ->sortable()
                    ->defaultImageUrl(url('/img/default-sm.png')),
                TextColumn::make('first_name')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('username')
                    ->sortable()
                    ->icon(fn ($record) => $record->isSuperUser()=='1' ? 'fas-crown' : '')
                    ->iconColor(fn ($record) => $record->isSuperUser()=='1' ? 'warning' : ''),
                TextColumn::make('email')
                    ->toggleable()
                    ->url(fn ($record) => 'mailto:'.$record->email, true)
                    ->sortable()
                    ->icon('heroicon-m-envelope'),
                TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->url(fn ($record) => 'tel:'.$record->phone, true)
                    ->sortable()
                    ->icon('heroicon-m-phone'),
                TextColumn::make('website')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->url(fn ($record) => $record->website, true)
                    ->sortable()
                    ->icon('heroicon-m-arrow-up-right'),
                TextColumn::make('assets_count')->counts('assets')
                    ->toggleable()
                    ->sortable()
                    ->label(new HtmlString(Blade::render('<x-fas-barcode class="w-5 h-5" />'))),
                TextColumn::make('accessories_count')->counts('accessories')
                    ->toggleable()
                    ->sortable()
                    ->label(new HtmlString(Blade::render('<x-far-keyboard class="w-6 h-6" />'))),
                TextColumn::make('licenses_count')->counts('licenses')
                    ->toggleable()
                    ->sortable()
                    ->label(new HtmlString(Blade::render('<x-far-save class="w-5 h-5" />'))),
                TextColumn::make('consumables_count')->counts('consumables')
                    ->toggleable()
                    ->sortable()
                    ->label(new HtmlString(Blade::render('<x-fas-tint class="w-5 h-5" />'))),
                ToggleColumn::make('activated')
                    ->toggleable()
                    ->sortable(),
                ToggleColumn::make('ldap_import')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('last_login')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
                TextColumn::make('admin.username')->label('Created by')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(UserImporter::class)->maxRows(100000),
                ExportAction::make()
                    ->exporter(UserExporter::class)
                    ->fileName(fn (Export $export): string => "users-{$export->getKey()}.csv")
            ])

            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    ReplicateAction::make()
                        ->excludeAttributes(
                            [
                                'password',
                                'username',
                                'remember_token',
                                'avatar',
                                'scim_externalid',
                                'two_factor_secret',
                            ]),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
                // ...
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool =>
                ($record->id != Auth::user()->id && ($record->isDeletable()))

            )
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()->exporter(UserExporter::class)
                ]),
            ])
            ->filters([
                Filter::make('Can Login')
                    ->query(fn (Builder $query): Builder => $query->where('activated', true)),
                Filter::make('LDAP Login')
                    ->query(fn (Builder $query): Builder => $query->where('ldap_import', true))
            ])
            ->deferLoading()
            ->searchable()
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    // This currently throws a missing parameter error on the user view
//    public static function getEloquentQuery(): Builder
//    {
//        return parent::getEloquentQuery()
//            ->select(['username', 'email']);
//    }
}
