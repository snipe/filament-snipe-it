<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\User;

class Accessory extends Model
{
    use HasFactory;

    protected $table = 'accessories';

    /**
     * Establishes the accessory -> category relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v3.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')->where('category_type', '=', 'accessory');
    }

    public function admin() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
