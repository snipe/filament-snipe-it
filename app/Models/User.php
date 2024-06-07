<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Asset;
use App\Models\Accessory;
use App\Models\Consumable;
use Illuminate\Support\Facades\Gate;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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

    /**
     * Checks if the user is deletable
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v6.3.4]
     * @return bool
     */
    public function isDeletable()
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
    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
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
        return $this->hasMany(\App\Models\User::class, 'manager_id');
    }


    /**
     * Establishes the user -> managed locations relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function managedLocations()
    {
        return $this->hasMany(\App\Models\Location::class, 'manager_id');
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }


}
