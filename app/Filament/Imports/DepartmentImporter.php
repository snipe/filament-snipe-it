<?php

namespace App\Filament\Imports;

use App\Models\Department;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DepartmentImporter extends Importer
{
    protected static ?string $model = Department::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('fax')
                ->rules(['max:20']),
            ImportColumn::make('phone')
                ->rules(['max:20']),
            ImportColumn::make('user_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('company_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('location_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('manager_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('notes')
                ->rules(['max:191']),
            ImportColumn::make('image')
                ->rules(['max:191']),
        ];
    }

    public function resolveRecord(): ?Department
    {
        if ($this->options['updateExisting'] ?? false) {
            return Department::firstOrNew([
                'name' => $this->data['name'],
            ]);
        }

        return new Department();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your department import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
