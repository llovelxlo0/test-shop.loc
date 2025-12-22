<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\CartItem;
use App\Models\Attribute;
use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goods extends Model
{
    use HasFactory;

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
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_values')->withPivot('value')->withTimestamps();
    }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'goods_id');
    }
    public function wishlistedByUsers()
    {
        return $this->belongsToMany(Goods::class, 'wishlist', 'user_id', 'goods_id')->withTimestamps();
    }
}
