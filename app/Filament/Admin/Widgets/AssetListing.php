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
use Filament\Widgets\TableWidget as BaseWidget;

class AssetListing extends BaseWidget
{
    protected static ?int $sort = 15;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Asset::query())
            ->columns([
                TextColumn::make('id')->toggleable()->sortable(),
                TextColumn::make('asset_tag')->sortable(),
                TextColumn::make('name')->sortable(),
                TextColumn::make('serial')->copyable()->sortable(),
                TextColumn::make('purchase_cost')->money('EUR', locale: 'pt')->sortable(),
                IconColumn::make('requestable')->boolean()->sortable(),
                TextColumn::make('asset_tag')->sortable(),
                TextColumn::make('purchase_date')->dateTime($format = 'F j, Y H:i:s')->sortable(),
                IconColumn::make('assigned_to')->boolean()->label('Checked Out')->sortable(),

            ])
            ->filters([
                TernaryFilter::make('Checked Out')
                    ->nullable()
                    ->attribute('assigned_to')
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->deferLoading()
            ->striped();
    }
}
