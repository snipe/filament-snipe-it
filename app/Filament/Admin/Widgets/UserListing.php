<?php

namespace App\Filament\Admin\Widgets;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Columns\ImageColumn;
use App\Models\User;
use Filament\Widgets\TableWidget as BaseWidget;

class UserListing extends BaseWidget
{
    protected static ?int $sort = 15;
    protected int | string | array $columnSpan = 'full';
    protected static bool $isDiscovered = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('id')->toggleable()->sortable(),
                ImageColumn::make('avatar')->toggleable()->sortable(),
                TextColumn::make('first_name')->toggleable()->sortable(),
                TextColumn::make('last_name')->toggleable()->sortable(),
                TextColumn::make('username')->toggleable()->sortable(),
                ViewColumn::make('email')->toggleable()->sortable()->view('tables.columns.email-link'),
                TextColumn::make('phone')->toggleable()->sortable()->icon('heroicon-m-phone'),
                IconColumn::make('activated')->toggleable()->boolean()->sortable(),
                TextColumn::make('created_at')->toggleable()->dateTime($format = 'F j, Y H:i:s')->sortable(),
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
