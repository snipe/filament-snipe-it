<?php

namespace App\Filament\Exports;

use App\Models\AssetModel;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AssetModelExporter extends Exporter
{
    protected static ?string $model = AssetModel::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('model_number'),
            ExportColumn::make('min_amt'),
            ExportColumn::make('manufacturer_id'),
            ExportColumn::make('category_id'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('depreciation_id'),
            ExportColumn::make('admin.username')->label('Created By'),
            ExportColumn::make('eol'),
            ExportColumn::make('image'),
            ExportColumn::make('deprecated_mac_address'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('fieldset_id'),
            ExportColumn::make('notes'),
            ExportColumn::make('requestable'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your asset model export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
