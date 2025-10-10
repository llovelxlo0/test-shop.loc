<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
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
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
