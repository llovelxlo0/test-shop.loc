<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Goods;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    protected $fillable = ['name', 'parent_id'];
    use HasFactory;

    public function parent() {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function goods() {
        return $this->hasMany(Goods::class);
    }

    public function attributes() {
        return $this->belongsToMany(Attribute::class, 'category_attributes')->withPivot('is_comparable')->withTimestamps();
    }
}
