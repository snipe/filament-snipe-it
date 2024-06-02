<?php

namespace App\Table\Components;

use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;

class ModelLinkColumn extends TextColumn
{
    protected string $view = 'tables.columns.model-link';

    protected function setUp(): void
    {
        parent::setUp();

        $this->url(function ($record) {
            if ($record === null) {
                return null;
            }

            $selectedResource = null;
            $relationship = Str::before($this->getName(), '.');
            $relatedRecord = $record->{$relationship};

            if ($relatedRecord === null) {
                return null;
            }

            $selectedResource = collect(Filament::getResources())
                ->first(fn ($resource) => $relatedRecord instanceof ($resource::getModel()));

            return $selectedResource::getUrl('view', [
                'record' => $relatedRecord->getKey()
            ]);
        });
    }
}
