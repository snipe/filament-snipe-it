<?php

namespace App\Filament\Imports;

use App\Models\Manufacturer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ManufacturerImporter extends Importer
{
    protected static ?string $model = Manufacturer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('user_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('url')
                ->rules(['max:191']),
            ImportColumn::make('support_url')
                ->rules(['max:191']),
            ImportColumn::make('warranty_lookup_url')
                ->rules(['max:191']),
            ImportColumn::make('support_phone')
                ->rules(['max:191']),
            ImportColumn::make('support_email')
                ->rules(['email', 'max:191']),
            ImportColumn::make('image')
                ->rules(['max:191']),
        ];
    }

    public function resolveRecord(): ?Manufacturer
    {
        if ($this->options['updateExisting'] ?? false) {
            return Manufacturer::firstOrNew([
                'name' => $this->data['name'],
            ]);
        }

        return new Manufacturer();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your manufacturer import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
