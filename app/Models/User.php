<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Asset;
use App\Models\Accessory;
use App\Models\Consumable;
use Illuminate\Support\Facades\Gate;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use ValidatingTrait;

    protected $rules = [
        'first_name'              => 'required|string|min:1|max:191',
        //'username'                => 'required|string|min:1|unique_undeleted|max:191',
        'username'                => 'required|string|min:1|max:191',
        'email'                   => 'email|nullable|max:191',
        'password'                => 'required|min:8',
        'locale'                  => 'max:10|nullable',
        'website'                 => 'url|nullable|max:191',
        //'manager_id'              => 'nullable|exists:users,id|cant_manage_self',
        'location_id'             => 'exists:locations,id|nullable',
        'start_date'              => 'nullable|date_format:Y-m-d',
        'end_date'                => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        'autoassign_licenses'     => 'boolean',
        'address'                 => 'max:191|nullable',
        'city'                    => 'max:191|nullable',
        'state'                   => 'min:2|max:191|nullable',
        'country'                 => 'min:2|max:191|nullable',
        'zip'                     => 'max:10|nullable',
        'vip'                     => 'boolean',
        'remote'                  => 'boolean',
        'activated'               => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'activated',
        'address',
        'city',
        'company_id',
        'country',
        'department_id',
        'email',
        'employee_num',
        'first_name',
        'jobtitle',
        'last_name',
        'ldap_import',
        'locale',
        'location_id',
        'manager_id',
        'password',
        'phone',
        'notes',
        'state',
        'username',
        'zip',
        'remote',
        'start_date',
        'end_date',
        'scim_externalid',
        'avatar',
        'gravatar',
        'vip',
        'autoassign_licenses',
        'website',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Checks if the user is deletable
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v6.3.4]
     * @return bool
     */
    public function isDeletable() : Bool
    {
        return Gate::allows('delete', $this)
            && ($this->assets->count() === 0)
            && ($this->licenses->count() === 0)
            && ($this->consumables->count() === 0)
            && ($this->accessories->count() === 0)
            && ($this->managedLocations->count() === 0)
            && ($this->managesUsers->count() === 0)
            && ($this->deleted_at == '');
    }

    /**
     * Checks if the user is a SuperUser
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v1.0]
     * @return bool
     */
    public function isSuperUser() : Bool
    {
        return $this->checkPermissionSection('superuser');
    }

    /**
     * Establishes the user -> groups relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function groups()
    {
        return $this->belongsToMany(\App\Models\Group::class, 'users_groups');
    }


    /**
     * Internally check the user permission for the given section
     *
     * @return bool
     */
    protected function checkPermissionSection($section)
    {
        $user_groups = $this->groups;
        if (($this->permissions == '') && (count($user_groups) == 0)) {

            return false;
        }

        $user_permissions = json_decode($this->permissions, true);

        $is_user_section_permissions_set = ($user_permissions != '') && array_key_exists($section, $user_permissions);
        //If the user is explicitly granted, return true
        if ($is_user_section_permissions_set && ($user_permissions[$section] == '1')) {
            return true;
        }
        // If the user is explicitly denied, return false
        if ($is_user_section_permissions_set && ($user_permissions[$section] == '-1')) {
            return false;
        }

        // Loop through the groups to see if any of them grant this permission
        foreach ($user_groups as $user_group) {
            $group_permissions = (array) json_decode($user_group->permissions, true);
            if (((array_key_exists($section, $group_permissions)) && ($group_permissions[$section] == '1'))) {
                return true;
            }
        }

        return false;
    }

    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function decodePermissions()
    {
        return json_decode($this->permissions, true);
    }


    /**
     * Establishes the user -> assets relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assets()
    {
        return $this->morphMany(Asset::class, 'assigned', 'assigned_type', 'assigned_to')->orderBy('id');
    }

    /**
     * Establishes the user -> accessories relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function accessories()
    {
        return $this->belongsToMany(Accessory::class, 'accessories_users', 'assigned_to', 'accessory_id')
            ->withPivot('id', 'created_at', 'note')->orderBy('accessory_id');
    }

    /**
     * Establishes the user -> consumables relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v3.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function consumables()
    {
        return $this->belongsToMany(Consumable::class, 'consumables_users', 'assigned_to', 'consumable_id')->withPivot('id','created_at','note');
    }

    /**
     * Establishes the user -> license seats relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function licenses()
    {
        return $this->belongsToMany(License::class, 'license_seats', 'assigned_to', 'license_id')->withPivot('id', 'created_at', 'updated_at');
    }

    /**
     * Establishes the user -> managed users relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v6.4.1]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function managesUsers()
    {
        return $this->hasMany(User::class, 'manager_id');
    }


    /**
     * Establishes the user -> managed locations relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function managesLocations()
    {
        return $this->hasMany(Location::class, 'manager_id');
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Establishes the user -> department relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    /**
     * Establishes the user -> location relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v3.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class, 'location_id');
    }

    /**
     * Establishes the user -> manager relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function manager()
    {
        return $this->belongsTo(self::class, 'manager_id')->withTrashed();
    }

    /**
     * Query builder scope to order on admin user
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param string                              $order         Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderByCreatedBy($query, $order)
    {
        // Left join here, or it will only return results with parents
        return $query->leftJoin('users as admin_user', 'users.created_by', '=', 'admin_user.id')
            ->orderBy('admin_user.first_name', $order)
            ->orderBy('admin_user.last_name', $order);
    }



}
