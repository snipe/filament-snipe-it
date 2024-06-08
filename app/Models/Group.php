<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;

class Group extends Model
{
    use HasFactory;
    use ValidatingTrait;

    protected $table = 'permission_groups';

    public $rules = [
        'name' => 'required|min:2|max:255|unique',
    ];

    protected $fillable = [
        'name',
        'permissions'
    ];
}
