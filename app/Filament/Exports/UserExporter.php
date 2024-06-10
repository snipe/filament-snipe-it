<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('first_name'),
            ExportColumn::make('last_name'),
            ExportColumn::make('company.name'),
            ExportColumn::make('email'),
            ExportColumn::make('permissions'),
            ExportColumn::make('activated'),
            ExportColumn::make('admin.username')->label('Created By'),
            ExportColumn::make('last_login'),
//            ExportColumn::make('assets_count')->withCount('assets'),
//            ExportColumn::make('accessories_count')->counts('accessories'),
//            ExportColumn::make('consumables_count')->counts('consumables'),
//            ExportColumn::make('licenses_count')->counts('licenses'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('website'),
            ExportColumn::make('country'),
            ExportColumn::make('gravatar'),
            ExportColumn::make('location.name'),
            ExportColumn::make('phone'),
            ExportColumn::make('jobtitle'),
            ExportColumn::make('manager_id'),
            ExportColumn::make('employee_num'),
            ExportColumn::make('avatar'),
            ExportColumn::make('username'),
            ExportColumn::make('notes'),
            ExportColumn::make('ldap_import'),
            ExportColumn::make('locale'),
//            ExportColumn::make('two_factor_enrolled'),
//            ExportColumn::make('two_factor_optin'),
//            ExportColumn::make('department.name'),
//            ExportColumn::make('address'),
//            ExportColumn::make('address2'),
//            ExportColumn::make('city'),
//            ExportColumn::make('state'),
//            ExportColumn::make('zip'),
//            ExportColumn::make('remote'),
            ExportColumn::make('start_date'),
            ExportColumn::make('end_date'),
            ExportColumn::make('autoassign_licenses'),
            ExportColumn::make('vip'),
            ExportColumn::make('theme'),
            ExportColumn::make('theme_color'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
