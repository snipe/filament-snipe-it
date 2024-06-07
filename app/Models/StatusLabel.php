<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLabel extends Model
{
    use HasFactory;

    protected $table = 'status_labels';

    protected $fillable = [
        'archived',
        'deployable',
        'name',
        'notes',
        'pending',
    ];


    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Establishes the status label -> assets relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assets()
    {
        return $this->hasMany(\App\Models\Asset::class, 'status_id');
    }


    /**
     * Query builder scope for deployable status types
     *
     * @return \Illuminate\Database\Query\Builder Modified query builder
     */
    public function scopeDeployable()
    {
        return $this->where('pending', '=', 0)
            ->where('archived', '=', 0)
            ->where('deployable', '=', 1);
    }
}
