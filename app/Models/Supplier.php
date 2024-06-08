<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $rules = [
        'name'               => 'required|min:1|max:255|unique_undeleted',
        'fax'               => 'min:7|max:35|nullable',
        'phone'             => 'min:7|max:35|nullable',
        'contact'           => 'max:100|nullable',
        'notes'             => 'max:191|nullable', // Default string length is 191 characters..
        'email'             => 'email|max:150|nullable',
        'address'            => 'max:250|nullable',
        'address2'           => 'max:250|nullable',
        'city'               => 'max:191|nullable',
        'state'              => 'min:2|max:191|nullable',
        'country'            => 'min:2|max:191|nullable',
        'zip'               => 'max:10|nullable',
        'url'               => 'sometimes|nullable|string|max:250',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'address2',
        'city',
        'state',
        'country',
        'zip',
        'phone',
        'fax',
        'email',
        'contact',
        'url',
        'notes']
    ;

    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
