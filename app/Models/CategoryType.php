<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    protected $fillable = ['name'];

    public function categoryNames() {
        return $this->hasMany(CategoryName::class, 'category_type_id');
    }


}
