<?php

namespace App\Filament\Imports;

use App\Models\Location;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class LocationImporter extends Importer
{
    protected static ?string $model = Location::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['max:191']),
            ImportColumn::make('city')
                ->rules(['max:191']),
            ImportColumn::make('state')
                ->rules(['max:191']),
            ImportColumn::make('country')
                ->rules(['max:191']),
            ImportColumn::make('user_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('address')
                ->rules(['max:191']),
            ImportColumn::make('address2')
                ->rules(['max:191']),
            ImportColumn::make('zip')
                ->rules(['max:10']),
            ImportColumn::make('fax')
                ->rules(['max:20']),
            ImportColumn::make('phone')
                ->rules(['max:20']),
            ImportColumn::make('parent_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('currency')
                ->rules(['max:10']),
            ImportColumn::make('ldap_ou')
                ->rules(['max:191']),
            ImportColumn::make('manager_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('image')
                ->rules(['max:191']),
        ];
    }

    public function resolveRecord(): ?Location
    {
        if ($this->options['updateExisting'] ?? false) {
            return Location::firstOrNew([
                'name' => $this->data['name'],
            ]);
        }

        return new Location();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your location import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
