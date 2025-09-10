<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    protected $fillable = ['name'];
    protected $table = 'category_types';

    public function goods() {
        return $this->hasMany(Goods::class, 'category_type_id');
    }


}
