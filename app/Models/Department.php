<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

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
