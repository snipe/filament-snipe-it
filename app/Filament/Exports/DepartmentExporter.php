<?php

namespace App\Filament\Exports;

use App\Models\Department;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class DepartmentExporter extends Exporter
{
    protected static ?string $model = Department::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('fax'),
            ExportColumn::make('phone'),
            ExportColumn::make('admin.username')->label('Created By'),
            ExportColumn::make('company_id'),
            ExportColumn::make('location_id'),
            ExportColumn::make('manager_id'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('image'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your department export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
