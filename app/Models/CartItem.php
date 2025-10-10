<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'goods_id',
        'quantity',
        'price'
    ];

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
