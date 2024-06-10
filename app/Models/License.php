<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class License extends Model
{
    use HasFactory;
    use ValidatingTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'depreciation_id',
        'expiration_date',
        'license_email',
        'license_name', //actually licensed_to
        'maintained',
        'manufacturer_id',
        'category_id',
        'name',
        'notes',
        'order_number',
        'purchase_cost',
        'purchase_date',
        'purchase_order',
        'reassignable',
        'seats',
        'serial',
        'supplier_id',
        'termination_date',
        'free_seat_count',
        'user_id',
        'min_amt',
    ];

    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Establishes the license -> company relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    /**
     * Establishes the license -> category relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    /**
     * Establishes the license -> manufacturer relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function manufacturer()
    {
        return $this->belongsTo(\App\Models\Manufacturer::class, 'manufacturer_id');
    }

    /**
     * Determine whether the user should be emailed on checkin/checkout
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v2.0]
     * @return bool
     */
    public function checkin_email()
    {
        if ($this->category) {
            return $this->category->checkin_email;
        }
        return false;
    }

    /**
     * Determine whether the user should be required to accept the license
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return bool
     */
    public function requireAcceptance()
    {
        if ($this->category) {
            return $this->category->require_acceptance;
        }

        return false;
    }



    /**
     * Establishes the license -> assigned user relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assignedusers()
    {
        return $this->belongsToMany(User::class, 'license_seats', 'license_id', 'assigned_to');
    }

}
