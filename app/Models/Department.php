<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

class Department extends Model
{
    use HasFactory;
    use ValidatingTrait;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'fax',
        'location_id',
        'company_id',
        'manager_id',
        'notes',
    ];

    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Establishes the department -> company relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    /**
     * Establishes the department -> users relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class, 'department_id');
    }

    /**
     * Establishes the department -> manager relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function manager()
    {
        return $this->belongsTo(\App\Models\User::class, 'manager_id');
    }

    /**
     * Establishes the department -> location relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class, 'location_id');
    }

    /**
     * Query builder scope to order on location name
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderLocation($query, $order)
    {
        return $query->leftJoin('locations as department_location', 'departments.location_id', '=', 'department_location.id')->orderBy('department_location.name', $order);
    }

    /**
     * Query builder scope to order on manager name
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderManager($query, $order)
    {
        return $query->leftJoin('users as department_user', 'departments.manager_id', '=', 'department_user.id')->orderBy('department_user.first_name', $order)->orderBy('department_user.last_name', $order);
    }
}
