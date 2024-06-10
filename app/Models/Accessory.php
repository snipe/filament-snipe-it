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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'company_id',
        'location_id',
        'name',
        'order_number',
        'purchase_cost',
        'purchase_date',
        'model_number',
        'manufacturer_id',
        'supplier_id',
        'image',
        'qty',
        'min_amt',
        'requestable',
        'notes',
    ];

    /**
     * Accessory validation rules
     */
    public $rules = [
        'name'              => 'required|min:3|max:255',
        'qty'               => 'required|integer|min:1',
        'category_id'       => 'required|integer|exists:categories,id',
        'company_id'        => 'integer|nullable',
        'min_amt'           => 'integer|min:0|nullable',
        'purchase_cost'     => 'numeric|nullable|gte:0',
        'purchase_date'   => 'date_format:Y-m-d|nullable',
    ];

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
