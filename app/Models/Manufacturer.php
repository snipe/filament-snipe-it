<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manufacturer extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'manufacturers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'support_email',
        'support_phone',
        'support_url',
        'url',
        'warranty_lookup_url',
    ];


    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }


//    public function assets()
//    {
//        return $this->hasManyThrough(\App\Models\Asset::class, \App\Models\AssetModel::class, 'manufacturer_id', 'model_id');
//    }

//    public function models()
//    {
//        return $this->hasMany(\App\Models\AssetModel::class, 'manufacturer_id');
//    }
//
//    public function licenses()
//    {
//        return $this->hasMany(\App\Models\License::class, 'manufacturer_id');
//    }

    public function accessories()
    {
        return $this->hasMany(\App\Models\Accessory::class, 'manufacturer_id');
    }

//    public function consumables()
//    {
//        return $this->hasMany(\App\Models\Consumable::class, 'manufacturer_id');
//    }
}
