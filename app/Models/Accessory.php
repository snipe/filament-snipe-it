<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

class Accessory extends Model
{
    use HasFactory;
    use ValidatingTrait;
    use SoftDeletes;

    protected $table = 'accessories';

    /**
     * Establishes the accessory -> category relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v3.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id')->where('category_type', '=', 'accessory');
    }

    /**
     * Determine which user created this resource
     * @return BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
