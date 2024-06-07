<?php

namespace App\Filament\Imports;

use App\Models\Asset;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AssetImporter extends Importer
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['max:191']),
            ImportColumn::make('asset_tag')
                ->rules(['max:191']),
            ImportColumn::make('model_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('serial')
                ->rules(['max:191']),
            ImportColumn::make('purchase_date')
                ->rules(['date']),
            ImportColumn::make('asset_eol_date')
                ->rules(['date']),
            ImportColumn::make('eol_explicit')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('purchase_cost')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('order_number')
                ->rules(['max:191']),
            ImportColumn::make('assigned_to')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('notes'),
            ImportColumn::make('image'),
            ImportColumn::make('user_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('physical')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('status_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('archived')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('warranty_months')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('depreciate')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('supplier_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('requestable')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('rtd_location_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('accepted')
                ->rules(['max:191']),
            ImportColumn::make('last_checkout')
                ->rules(['datetime']),
            ImportColumn::make('last_checkin')
                ->rules(['datetime']),
            ImportColumn::make('expected_checkin')
                ->rules(['date']),
            ImportColumn::make('company_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('assigned_type')
                ->rules(['max:191']),
            ImportColumn::make('last_audit_date')
                ->rules(['datetime']),
            ImportColumn::make('next_audit_date')
                ->rules(['date']),
            ImportColumn::make('location_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('checkin_counter')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('checkout_counter')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('requests_counter')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('byod')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('_snipeit_imei_1'),
            ImportColumn::make('_snipeit_phone_number_2'),
            ImportColumn::make('_snipeit_ram_3'),
            ImportColumn::make('_snipeit_cpu_4'),
            ImportColumn::make('_snipeit_mac_address_5'),
            ImportColumn::make('_snipeit_test_encrypted_6'),
            ImportColumn::make('_snipeit_test_checkbox_7'),
            ImportColumn::make('_snipeit_test_radio_8'),
        ];
    }

    public function resolveRecord(): ?Asset
    {
        // return Asset::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Asset();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your asset import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
