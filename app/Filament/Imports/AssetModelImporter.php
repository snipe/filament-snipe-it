<?php

namespace App\Filament\Imports;

use App\Models\AssetModel;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AssetModelImporter extends Importer
{
    protected static ?string $model = AssetModel::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('model_number')
                ->rules(['max:191']),
            ImportColumn::make('min_amt')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('manufacturer_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('category_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('depreciation_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('user_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('eol')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('image')
                ->rules(['max:191']),
            ImportColumn::make('deprecated_mac_address')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('fieldset_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('notes'),
            ImportColumn::make('requestable')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): ?AssetModel
    {
        if ($this->options['updateExisting'] ?? false) {
            return AssetModel::firstOrNew([
                'name' => $this->data['name'],
            ]);
        }

        return new AssetModel();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your asset model import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
