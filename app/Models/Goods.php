<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CategoryType;
use App\Models\CategoryName;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goods extends Model
{
    protected $fillable = [
        'name',
        'description', 
        'price', 
        'image',
        'category_name_id',
        'category_type_id'
    ];

    public function categoryName() {
        return $this->belongsTo(CategoryName::class, 'category_name_id');
    }
    public function categoryType() {
        return $this->belongsTo(CategoryType::class, 'category_type_id');
    }
}
