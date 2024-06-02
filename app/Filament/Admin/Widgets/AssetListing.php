<?php

namespace App\Filament\Admin\Widgets;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
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
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Asset::query())
            ->columns([
                TextColumn::make('asset_tag'),
                TextColumn::make('name'),
                TextColumn::make('serial'),
                TextColumn::make('purchase_cost'),
                IconColumn::make('requestable')->boolean(),
                TextColumn::make('asset_tag'),
                TextColumn::make('purchase_date'),
                IconColumn::make('assigned_to')->boolean()->label('Checked Out'),

            ])
            ->filters([
                TernaryFilter::make('Checked Out')
                    ->nullable()
                    ->attribute('assigned_to')
            ])

            ->actions([
                EditAction::make(),
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
