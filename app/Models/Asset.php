<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StatusLabel;
use App\Models\AssetModel;
use App\Models\Company;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'assets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'asset_tag',
        'assigned_to',
        'assigned_type',
        'company_id',
        'image',
        'location_id',
        'model_id',
        'name',
        'notes',
        'order_number',
        'purchase_cost',
        'purchase_date',
        'rtd_location_id',
        'serial',
        'status_id',
        'supplier_id',
        'warranty_months',
        'requestable',
        'last_checkout',
        'expected_checkin',
        'byod',
        'asset_eol_date',
        'eol_explicit',
        'last_audit_date',
        'next_audit_date',
        'asset_eol_date',
    ];



    /**
     * Establishes the asset -> model relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assetmodel()
    {
        return $this->belongsTo(AssetModel::class, 'model_id');
    }

    public function statuslabel()
    {
        return $this->belongsTo(\App\Models\Statuslabel::class, 'status_id');
    }

    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Establishes the asset -> company relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v3.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Establishes the asset -> location relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class, 'location_id');
    }

    /**
     * Establishes the asset -> aupplier relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class, 'supplier_id');
    }

    /**
     * Get maintenances for this asset
     *
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @since 1.0
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function maintenances()
    {
        return $this->hasMany(\App\Models\AssetMaintenance::class, 'asset_id')
            ->orderBy('created_at', 'desc');
    }

}
