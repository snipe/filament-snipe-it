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
}
