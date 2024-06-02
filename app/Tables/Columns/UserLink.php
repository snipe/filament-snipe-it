<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class UserLink extends TextColumn
{
    protected string $view = 'tables.columns.user-link';

    protected function setUp(): void
    {
        parent::setUp();

        $this->url(function ($record) {
            if ($record === null) {
                return null;
            }

            return '<a href="mailto:'.$record->email.'">'.$record->email.'</a>';

        });
    }

}
