<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'permission_groups';

    public $rules = [
        'name' => 'required|min:2|max:255|unique',
    ];

    protected $fillable = [
        'name',
        'permissions'
    ];
}
