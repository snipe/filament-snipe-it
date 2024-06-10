<?php

namespace App\Filament\Exports;

use App\Models\Accessory;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AccessoryExporter extends Exporter
{
    protected static ?string $model = Accessory::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('category.name'),
            ExportColumn::make('user_id'),
            ExportColumn::make('qty'),
            ExportColumn::make('requestable'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('location_id'),
            ExportColumn::make('purchase_date'),
            ExportColumn::make('purchase_cost'),
            ExportColumn::make('order_number'),
            ExportColumn::make('company_id'),
            ExportColumn::make('min_amt'),
            ExportColumn::make('manufacturer_id'),
            ExportColumn::make('model_number'),
            ExportColumn::make('image'),
            ExportColumn::make('supplier_id'),
            ExportColumn::make('notes'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your accessory export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
