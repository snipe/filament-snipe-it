<?php

namespace App\Filament\Admin\Widgets;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class AssetListing extends BaseWidget
{
    protected static ?int $sort = 15;
    protected static string $chartId = 'assetsChart';
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Asset::query()->latest();
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(Asset::query())
            ->columns([
                TextColumn::make('id')->toggleable()->sortable(),
                TextColumn::make('asset_tag')->sortable(),
                TextColumn::make('name')->toggleable()->sortable(),
                TextColumn::make('serial')->toggleable()->copyable()->sortable(),
                TextColumn::make('purchase_cost')->toggleable()->money('EUR', locale: 'pt')->sortable(),
                IconColumn::make('requestable')->toggleable()->boolean()->sortable(),
                TextColumn::make('purchase_date')->toggleable()->dateTime($format = 'F j, Y H:i:s')->sortable(),
                IconColumn::make('assigned_to')->toggleable()->boolean()->label('Checked Out')->sortable(),

            ])
            ->deferLoading()
            ->striped();
    }


    protected function getTableFilters(): array
    {
        return [
            Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from'),
                    DatePicker::make('created_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
        ];
    }
}
