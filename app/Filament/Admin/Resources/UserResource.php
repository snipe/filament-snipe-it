<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Location;
use App\Models\Department;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
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
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Infolists;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Enums\IconPosition;
use Filament\Resources\Concerns\HasTabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;


class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $recordTitleAttribute = 'first_name';
    protected static int $globalSearchResultsLimit = 10;

    use HasTabs;



    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([

                        // Set schema for infolist tab
                        Tabs\Tab::make('Details')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('username'),
                                TextEntry::make('email')
                                    ->icon('heroicon-m-envelope')
                                    ->url(fn (User $record): string => 'mailto:'.$record->email),
                                ImageEntry::make('avatar')
                                    ->label('')
                                    ->circular(),
                                TextEntry::make('location.name')
                                    ->label('Location')
                                    ->icon('fas-location-dot')
                                    ->url(fn (User $record): string => route('filament.admin.settings.resources.locations.edit', ['record' => $record])),
                                TextEntry::make('jobtitle'),
                                TextEntry::make('phone')
                                    ->icon('fas-square-phone')
                                    ->url(fn (User $record): string => ($record->phone ? 'tel://'.$record->phone: '')),
                                TextEntry::make('website')
                                    ->icon('fas-square-arrow-up-right')
                                    ->url(fn (User $record): string => ($record->website ?? '')),
                                TextEntry::make('manager.name')
                                    ->icon('fas-user-tie'),
                                TextEntry::make('notes')
                                    ->toggleable(isToggledHiddenByDefault: true)
                                    ->searchable()
                                    ->sortable(),
                            ])->columns(4)
                            ->icon('fas-address-card'),


                        // Set assets table tab
                        Tabs\Tab::make('Assets')
                            ->schema([
                                ViewEntry::make('assets')
                                    ->view('livewire.view-asset')
                            ])
                            ->icon('fas-barcode')
                            ->badge(fn ($record) => $record->assets->count()),

                        // Set accessories table tab
                        Tabs\Tab::make('Accessories')
                            ->schema([
                                ViewEntry::make('accessories')
                                    ->view('livewire.view-accessory')
                            ])
                            ->icon('fas-keyboard')
                            ->badge(fn ($record) => $record->accessories->count()),

                        // Set licenses table tab
                        Tabs\Tab::make('Licenses')
                            ->schema([
                                ViewEntry::make('licenses')
                                    ->view('livewire.view-license')
                            ])
                            ->icon('fas-save')
                            ->badge(fn ($record) => $record->licenses->count()),

                        // Set consumables table tab
                        Tabs\Tab::make('Consumables')
                            ->schema([
                                ViewEntry::make('consumables')
                                    ->view('livewire.view-consumable')
                            ])
                            ->icon('fas-tint')
                            ->badge(fn ($record) => $record->consumables->count()),

                        // Set uploads tab
                        Tabs\Tab::make('Uploads')
                            ->schema([
                                // ...
                            ])
                            ->icon('fas-paperclip'),

                        // Set history tab
                        Tabs\Tab::make('History')
                            ->schema([
                                // ...
                            ])
                            ->icon('far-clock'),

                        // Set managed locations tab
                        Tabs\Tab::make('Locations')
                            ->schema([
                                // ...
                            ])
                            ->icon('fas-location-dot'),

                        // Set managed users tab
                        Tabs\Tab::make('Users')
                            ->schema([
                                // ...
                            ])
                            ->icon('fas-people-roof'),
                    ])
                    ->persistTab()
                    ->persistTabInQueryString()

            ])
            ->columns(1);

    }

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
                        ->string()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create'),
                    Toggle::make('activated')
                        ->label('This user can login')
                        ->onIcon('fas-check-circle')
                        ->offIcon('fas-times-circle')
                        ->onColor('success')
                        ->offColor('gray')
                        ->default('on')
                ])
                ->columns(2),

                Section::make('Work Information')->schema([
                    TextInput::make('jobtitle')
                        ->maxLength(255),
                    TextInput::make('employee_num')
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(255),

                    Select::make('manager_id')
                        ->relationship(name: 'manager', titleAttribute: 'username')
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
                    DatePicker::make('start_date')
                        ->suffixIcon('fas-calendar')
                        ->native(false)
                        ->displayFormat('Y-m-d'),

                    DatePicker::make('end_date')
                        ->suffixIcon('fas-calendar')
                        ->native(false)
                        ->displayFormat('Y-m-d'),
                    ])
                    ->collapsible()
                    ->persistCollapsed()
                    ->id('user-workinfo')
                    ->columns(2),
                Section::make('Address')
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
                    TextInput::make('website')
                            ->maxLength(255),
                    TextInput::make('phone')
                        ->maxLength(255),
                    FileUpload::make('avatar')
                        ->directory('assets')
                        ->imageEditor()
                        ->image(),
                    Textarea::make('notes')
                            ->string(),
                        Toggle::make('vip')
                            ->onIcon('fas-check-circle')
                            ->offIcon('fas-times-circle')
                            ->onColor('success')
                            ->offColor('gray'),
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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('username')
                    ->sortable()
                    ->searchable()
                    ->icon(fn ($record) => $record->isSuperUser()=='1' ? 'fas-crown' : '')
                    ->iconColor(fn ($record) => $record->isSuperUser()=='1' ? 'warning' : ''),
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
                TextColumn::make('email')
                    ->toggleable()
                    ->searchable()
                    ->url(fn ($record) => 'mailto:'.$record->email, true)
                    ->sortable()
                    ->icon('heroicon-m-envelope'),
                TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->url(fn ($record) => 'tel:'.$record->phone, true)
                    ->searchable()
                    ->sortable()
                    ->icon('fas-square-phone'),
                TextColumn::make('company.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('manager.username')
                    ->label('Manager')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Location')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('website')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->url(fn ($record) => $record->website, true)
                    ->sortable()
                    ->icon('heroicon-m-arrow-up-right'),
                TextColumn::make('last_login')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime($format = 'F j, Y H:i:s')
                    ->sortable(),
                TextColumn::make('notes')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
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
                    RestoreAction::make(),
                ])->tooltip('Actions'),
                // ...
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool =>
                //($record->id != Auth::user()->id && ($record->isDeletable()))
                ($record->id != auth()->user()->id)

            )
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()->exporter(UserExporter::class)
                ]),
            ])
            ->filters([
                Filter::make('can_login')
                    ->label('Can login')
                    ->query(fn (Builder $query): Builder => $query->where('activated', true))
                    ->toggle(),
                Filter::make('ldap_login')
                    ->label('Managed via LDAP')
                    ->query(fn (Builder $query): Builder => $query->where('ldap_import', true))
                    ->toggle(),
                Filter::make('vip')
                    ->query(fn (Builder $query): Builder => $query->where('vip', true))
                    ->toggle(),

                Filter::make('one_asset')
                    ->label('Has at least one asset')
                    ->query(fn (Builder $query):
                        Builder => $query
                            ->has('assets','>', 0)
                        )
                    ->toggle(),
                Filter::make('one_accessory')
                    ->label('Has at least one accessory')
                    ->query(fn (Builder $query):
                    Builder => $query
                        ->has('accessories','>', 0)
                    )
                    ->toggle(),
                Filter::make('one_license')
                    ->label('Has at least one license')
                    ->query(fn (Builder $query):
                    Builder => $query
                        ->has('licenses','>', 0)
                    )
                    ->toggle(),

                SelectFilter::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('location_id')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('manager_id')
                    ->relationship('manager', 'username')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make('deleted_at'),
//                Filter::make('created_at')
//                    ->form([
//                        DatePicker::make('created_from')
//                            ->native(false),
//                        DatePicker::make('created_until')
//                            ->native(false)
//                            ->default(now())
//                    ])
//                    ->query(function (Builder $query, array $data): Builder {
//                        return $query
//                            ->when(
//                                $data['created_from'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
//                            )
//                            ->when(
//                                $data['created_until'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
//                            );
//                    }),
//                Filter::make('start_date')
//                    ->form([
//                        DatePicker::make('start_date')
//                            ->native(false),
//                        DatePicker::make('start_until')
//                            ->native(false)
//                            ->default(now())
//                    ])
//                    ->query(function (Builder $query, array $data): Builder {
//                        return $query
//                            ->when(
//                                $data['start_date'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
//                            )
//                            ->when(
//                                $data['start_until'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
//                            );
//                    }),
//                Filter::make('end_date')
//                    ->form([
//                        DatePicker::make('end_date')
//                            ->native(false),
//                        DatePicker::make('end_until')
//                            ->native(false)
//                            ->default(now())
//                    ])
//                    ->query(function (Builder $query, array $data): Builder {
//                        return $query
//                            ->when(
//                                $data['end_date'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '>=', $date),
//                            )
//                            ->when(
//                                $data['end_until'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
//                            );
//                    })

            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormWidth(MaxWidth::FourExtraLarge)
            ->filtersFormColumns(4)
            ->filtersFormSchema(fn (array $filters): array => [
                Section::make('')
                    //->description('These filters affect the visibility of the records in the table.')
                    ->schema([
                        $filters['can_login'],
                        $filters['ldap_login'],
                        $filters['vip'],
                        $filters['one_asset'],
                        $filters['one_license'],
                        $filters['one_accessory'],
                    ])->columns(4),

                Section::make('')
                    ->schema([
                        $filters['company_id'],
                        $filters['location_id'],
                        $filters['department_id'],
                        $filters['manager_id'],
                        $filters['deleted_at'],
                    ])->columns(3),

            ])

            ->defaultPaginationPageOption(25)
            ->searchable()
            ->extremePaginationLinks()
            ->paginated([10, 25, 50, 100, 200])
            ->deferLoading()
            //->persistFiltersInSession()
            //->persistSortInSession()
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'username', 'employee_num'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Username' => $record->username,
            'Email' => $record->email,
            'First Name' => $record->first_name,
            'Last Name' => $record->last_name,
        ];
    }
    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->first_name.' '.$record->last_name;
    }



}
