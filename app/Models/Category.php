<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AssetModel;
use App\Models\Accessory;
use App\Models\License;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;


    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Establishes the category -> assets relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assets()
    {
        return $this->hasManyThrough(Asset::class, AssetModel::class, 'category_id', 'model_id');
    }


    /**
     * Establishes the category -> accessories relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function accessories()
    {
        return $this->hasMany(Accessory::class);
    }

    /**
     * Establishes the category -> licenses relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.3]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function licenses()
    {
        return $this->hasMany(License::class);
    }

}
