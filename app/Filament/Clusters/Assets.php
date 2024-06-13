<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use App\Models\Asset;

class Assets extends Cluster
{
    protected static ?string $navigationIcon = 'fas-barcode';
    public static function getNavigationBadge(): ?string
    {
        return Asset::count();
    }

}
