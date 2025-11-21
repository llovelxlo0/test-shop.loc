<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name'];

    public function goods()
    {
        return $this->belongsToMany(Goods::class, 'attribute_values')->withPivot('value')->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_attributes')->withPivot('is_comparable')->withTimestamps();
    }
}
