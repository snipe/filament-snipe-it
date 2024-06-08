<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetMaintenance extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'asset_id',
        'supplier_id',
        'asset_maintenance_type',
        'is_warranty',
        'start_date',
        'completion_date',
        'asset_maintenance_time',
        'notes',
        'cost',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
