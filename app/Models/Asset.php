<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StatusLabel;
use App\Models\AssetModel;
use App\Models\Company;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'depreciation_id',
        'eol',
        'fieldset_id',
        'image',
        'manufacturer_id',
        'min_amt',
        'model_number',
        'name',
        'notes',
        'user_id',
    ];



    /**
     * Establishes the asset -> model relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function model()
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




}
