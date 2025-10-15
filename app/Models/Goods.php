<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goods extends Model
{
    protected $fillable = [
        'name',
        'description', 
        'price', 
        'image',
        'category_id',
        'stock'
    ];

    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'goods_id');
    }
}
