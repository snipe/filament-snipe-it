<?php

namespace App\Filament\Exports;

use App\Models\Manufacturer;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ManufacturerExporter extends Exporter
{
    protected static ?string $model = Manufacturer::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('admin.username')->label('Created By'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('url'),
            ExportColumn::make('support_url'),
            ExportColumn::make('warranty_lookup_url'),
            ExportColumn::make('support_phone'),
            ExportColumn::make('support_email'),
            ExportColumn::make('image'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your manufacturer export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
