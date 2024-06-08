<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class License extends Model
{
    use HasFactory;
    use ValidatingTrait;
    use SoftDeletes;

    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
