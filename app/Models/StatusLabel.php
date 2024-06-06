<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLabel extends Model
{
    use HasFactory;

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
