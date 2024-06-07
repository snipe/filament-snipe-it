<?php

namespace App\Filament\Imports;

use App\Models\Category;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CategoryImporter extends Importer
{
    protected static ?string $model = Category::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('user_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('eula_text'),
            ImportColumn::make('use_default_eula')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('require_acceptance')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('category_type')
                ->rules(['max:191']),
            ImportColumn::make('checkin_email')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'email', 'boolean']),
            ImportColumn::make('image')
                ->rules(['max:191']),
        ];
    }

    public function resolveRecord(): ?Category
    {
        if ($this->options['updateExisting'] ?? false) {
            return Category::firstOrNew([
                'name' => $this->data['name'],
            ]);
        }

        return new Category();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your category import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
