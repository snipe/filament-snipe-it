<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [

            ImportColumn::make('first_name')
                ->guess(['first name', 'firstname', 'given name'])
                ->example('Jane')
                ->rules(['max:191']),
            ImportColumn::make('last_name')
                ->guess(['last name', 'lastname', 'surname', 'family name'])
                ->example('Doe')
                ->rules(['max:191']),
            ImportColumn::make('username')
                ->requiredMapping()
                ->rules(['required','max:191']),
            ImportColumn::make('email')
                ->rules(['email', 'max:191']),
            ImportColumn::make('password')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
//            ImportColumn::make('activated')
//                ->requiredMapping()
//                ->boolean()
//                ->rules(['required', 'boolean']),

            ImportColumn::make('website')
                ->rules(['max:191']),
            ImportColumn::make('country')
                ->rules(['max:191']),
            ImportColumn::make('gravatar')
                ->rules(['max:191']),
            ImportColumn::make('location_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('phone')
                ->rules(['max:191']),
            ImportColumn::make('jobtitle')
                ->rules(['max:191']),
            ImportColumn::make('manager_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('employee_num'),
            ImportColumn::make('avatar')
                ->rules(['max:191']),
            ImportColumn::make('notes'),
            ImportColumn::make('company_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ldap_import')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('locale')
                ->rules(['max:10']),
            ImportColumn::make('department_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('address')
                ->rules(['max:191']),
            ImportColumn::make('address2')
                ->rules(['max:191']),
//            ImportColumn::make('city')
//                ->rules(['max:191']),
//            ImportColumn::make('state')
//                ->rules(['max:191']),
//            ImportColumn::make('zip')
//                ->rules(['max:10']),
//            ImportColumn::make('remote')
//                ->boolean()
//                ->rules(['boolean']),
//            ImportColumn::make('start_date')
//                ->rules(['date']),
//            ImportColumn::make('end_date')
//                ->rules(['date']),
//            ImportColumn::make('autoassign_licenses')
//                ->requiredMapping()
//                ->boolean()
//                ->rules(['required', 'boolean']),
//            ImportColumn::make('vip')
//                ->boolean()
//                ->rules(['boolean']),
//            ImportColumn::make('theme')
//                ->rules(['max:255']),
//            ImportColumn::make('theme_color')
//                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?User
    {

        if ($this->options['updateExisting'] ?? false) {
            return User::firstOrNew([
                'username' => $this->data['username'],
            ]);
        }

        return new User();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('updateExisting')
                ->label('Update existing records'),
        ];
    }
}
