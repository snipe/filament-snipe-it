<?php

namespace App\Filament\Exports;

use App\Models\AssetMaintenance;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AssetMaintenanceExporter extends Exporter
{
    protected static ?string $model = AssetMaintenance::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('asset_id'),
            ExportColumn::make('supplier_id'),
            ExportColumn::make('asset_maintenance_type'),
            ExportColumn::make('title'),
            ExportColumn::make('is_warranty'),
            ExportColumn::make('start_date'),
            ExportColumn::make('completion_date'),
            ExportColumn::make('asset_maintenance_time'),
            ExportColumn::make('notes'),
            ExportColumn::make('cost'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('user_id'),
            ExportColumn::make('assigned_type'),
            ExportColumn::make('assigned_to'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your asset maintenance export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
