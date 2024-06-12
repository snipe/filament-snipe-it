<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Rules\NonCircular;

class Location extends Model
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
        'name',
        'parent_id',
        'address',
        'address2',
        'city',
        'state',
        'country',
        'zip',
        'phone',
        'fax',
        'ldap_ou',
        'currency',
        'manager_id',
        'image',
    ];

    protected $rules = [
        'name'          => 'required|min:2|max:255',
        'address'       => 'max:191|nullable',
        'address2'      => 'max:191|nullable',
        'city'          => 'max:191|nullable',
        'state'         => 'min:2|max:191|nullable',
        'country'       => 'min:2|max:191|nullable',
        'zip'           => 'max:10|nullable',
        'manager_id'    => 'exists:users,id|nullable',
        //'parent_id'     => 'non_circular:locations,id',
        'parent_id'     => 'required','string','NonCircular' // I don't know if this works
    ];

    /**
     * Find the parent of a location
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id')
            ->with('parent');
    }

    /**
     * Find the manager of a location
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function manager()
    {
        return $this->belongsTo(\App\Models\User::class, 'manager_id');
    }

    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
