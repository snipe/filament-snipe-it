<?php

namespace App\Filament\Exports;

use App\Models\License;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class LicenseExporter extends Exporter
{
    protected static ?string $model = License::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('serial'),
            ExportColumn::make('purchase_date'),
            ExportColumn::make('purchase_cost'),
            ExportColumn::make('order_number'),
            ExportColumn::make('seats'),
            ExportColumn::make('notes'),
            ExportColumn::make('user_id'),
            ExportColumn::make('depreciation_id'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('license_name'),
            ExportColumn::make('license_email'),
            ExportColumn::make('depreciate'),
            ExportColumn::make('supplier_id'),
            ExportColumn::make('expiration_date'),
            ExportColumn::make('purchase_order'),
            ExportColumn::make('termination_date'),
            ExportColumn::make('maintained'),
            ExportColumn::make('reassignable'),
            ExportColumn::make('company_id'),
            ExportColumn::make('manufacturer_id'),
            ExportColumn::make('category_id'),
            ExportColumn::make('min_amt'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your license export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
