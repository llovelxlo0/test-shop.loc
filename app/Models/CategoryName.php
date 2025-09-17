<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryName extends Model
{
    protected $fillable = ['name', 'category_type_id'];

    public function categoryType() {
        return $this->belongsTo(CategoryType::class, 'category_type_id');
    }

}

