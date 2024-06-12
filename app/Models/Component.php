<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

class Component extends Model
{
    use HasFactory;
    use ValidatingTrait;
    use SoftDeletes;

    public $rules = [
        'name'           => 'required|min:3|max:255',
        'qty'            => 'required|integer|min:1',
        'category_id'    => 'required|integer|exists:categories,id',
        'supplier_id'    => 'nullable|integer|exists:suppliers,id',
        'company_id'     => 'integer|nullable|exists:companies,id',
        'min_amt'        => 'integer|min:0|nullable',
        'purchase_date'   => 'date_format:Y-m-d|nullable',
        'purchase_cost'  => 'numeric|nullable|gte:0',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'company_id',
        'supplier_id',
        'location_id',
        'name',
        'purchase_cost',
        'purchase_date',
        'min_amt',
        'order_number',
        'qty',
        'serial',
        'notes',
    ];

    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
