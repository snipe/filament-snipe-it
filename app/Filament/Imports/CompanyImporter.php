<?php

namespace App\Filament\Imports;

use App\Models\Company;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CompanyImporter extends Importer
{
    protected static ?string $model = Company::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('address')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('address2')
                ->rules(['max:191']),
            ImportColumn::make('city')
                ->rules(['max:191']),
            ImportColumn::make('state')
                ->rules(['max:191']),
            ImportColumn::make('zip')
                ->rules(['max:191']),
            ImportColumn::make('country')
                ->rules(['max:191']),
            ImportColumn::make('fax')
                ->rules(['max:20']),
            ImportColumn::make('email')
                ->rules(['email', 'max:150']),
            ImportColumn::make('phone')
                ->rules(['max:20']),
            ImportColumn::make('image')
                ->rules(['max:191']),
        ];
    }

    public function resolveRecord(): ?Company
    {
        if ($this->options['updateExisting'] ?? false) {
            return Company::firstOrNew([
                'name' => $this->data['name'],
            ]);
        }

        return new Company();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your company import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
