<?php

namespace App\Filament\Exports;

use App\Models\Asset;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AssetExporter extends Exporter
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('asset_tag'),
            ExportColumn::make('model_id'),
            ExportColumn::make('serial'),
            ExportColumn::make('purchase_date'),
            ExportColumn::make('asset_eol_date'),
            ExportColumn::make('eol_explicit'),
            ExportColumn::make('purchase_cost'),
            ExportColumn::make('order_number'),
            ExportColumn::make('assigned_to'),
            ExportColumn::make('notes'),
            ExportColumn::make('image'),
            ExportColumn::make('user_id'),
            ExportColumn::make('created_at'),
            ExportColumn::make('admin.username')->label('Created By'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('physical'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('status_id'),
            ExportColumn::make('archived'),
            ExportColumn::make('warranty_months'),
            ExportColumn::make('depreciate'),
            ExportColumn::make('supplier_id'),
            ExportColumn::make('requestable'),
            ExportColumn::make('rtd_location_id'),
            ExportColumn::make('accepted'),
            ExportColumn::make('last_checkout'),
            ExportColumn::make('last_checkin'),
            ExportColumn::make('expected_checkin'),
            ExportColumn::make('company_id'),
            ExportColumn::make('assigned_type'),
            ExportColumn::make('last_audit_date'),
            ExportColumn::make('next_audit_date'),
            ExportColumn::make('location_id'),
            ExportColumn::make('checkin_counter'),
            ExportColumn::make('checkout_counter'),
            ExportColumn::make('requests_counter'),
            ExportColumn::make('byod'),
            ExportColumn::make('_snipeit_imei_1'),
            ExportColumn::make('_snipeit_phone_number_2'),
            ExportColumn::make('_snipeit_ram_3'),
            ExportColumn::make('_snipeit_cpu_4'),
            ExportColumn::make('_snipeit_mac_address_5'),
            ExportColumn::make('_snipeit_test_encrypted_6'),
            ExportColumn::make('_snipeit_test_checkbox_7'),
            ExportColumn::make('_snipeit_test_radio_8'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your asset export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
