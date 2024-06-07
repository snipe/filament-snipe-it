<?php

namespace App\Filament\Imports;

use App\Models\Supplier;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SupplierImporter extends Importer
{
    protected static ?string $model = Supplier::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('address')
                ->rules(['max:250']),
            ImportColumn::make('address2')
                ->rules(['max:250']),
            ImportColumn::make('city')
                ->rules(['max:191']),
            ImportColumn::make('state')
                ->rules(['max:191']),
            ImportColumn::make('country')
                ->rules(['max:2']),
            ImportColumn::make('phone')
                ->rules(['max:35']),
            ImportColumn::make('fax')
                ->rules(['max:35']),
            ImportColumn::make('email')
                ->rules(['email', 'max:150']),
            ImportColumn::make('contact')
                ->rules(['max:100']),
            ImportColumn::make('notes')
                ->rules(['max:191']),
            ImportColumn::make('user_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('zip')
                ->rules(['max:10']),
            ImportColumn::make('url')
                ->rules(['max:250']),
            ImportColumn::make('image')
                ->rules(['max:191']),
        ];
    }

    public function resolveRecord(): ?Supplier
    {
        if ($this->options['updateExisting'] ?? false) {
            return Supplier::firstOrNew([
                'name' => $this->data['name'],
            ]);
        }

        return new Supplier();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your supplier import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
