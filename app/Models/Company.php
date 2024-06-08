<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;

class Company extends Model
{
    use HasFactory;

    // Declare the rules for the model validation
    protected $rules = [
        'name' => 'required|min:1|max:255|unique:companies,name',
        'fax' => 'min:7|max:35|nullable',
        'phone' => 'min:7|max:35|nullable',
        'email' => 'email|max:150|nullable',
    ];


    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'company_id');
    }
}
