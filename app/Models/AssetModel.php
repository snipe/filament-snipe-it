<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'models';

    /**
     * Establishes the model -> assets relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     * @since [v1.0]
     * @author [A. Gianotto] [<snipe@snipe.net>]
     */
    public function assets()
    {
        return $this->hasMany(Asset::class, 'model_id');
    }

    /**
     * Establishes the model -> category relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     * @since [v1.0]
     * @author [A. Gianotto] [<snipe@snipe.net>]
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Establishes the model -> category relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     * @since [v1.0]
     * @author [A. Gianotto] [<snipe@snipe.net>]
     */
    public function assetCategories()
    {
        return $this->hasMany(Category::class, 'category_id')->where('category_type', 'asset');
    }
}
