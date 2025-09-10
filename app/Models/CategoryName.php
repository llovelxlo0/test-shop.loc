<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryName extends Model
{
    protected $fillable = ['name'];
    protected $table = 'category_names';

    public function goods() {
        return $this->hasMany(Goods::class, 'category_name_id');
    }

}

