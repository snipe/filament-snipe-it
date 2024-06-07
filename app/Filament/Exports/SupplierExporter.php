<?php

namespace App\Filament\Exports;

use App\Models\Supplier;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SupplierExporter extends Exporter
{
    protected static ?string $model = Supplier::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('address'),
            ExportColumn::make('address2'),
            ExportColumn::make('city'),
            ExportColumn::make('state'),
            ExportColumn::make('country'),
            ExportColumn::make('phone'),
            ExportColumn::make('fax'),
            ExportColumn::make('email'),
            ExportColumn::make('contact'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('admin.username')->label('Created By'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('zip'),
            ExportColumn::make('url'),
            ExportColumn::make('image'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your supplier export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
