<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'company_id');
    }
}
