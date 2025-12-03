<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'goods_id',
        'user_id',
        'rating',
        'comment',
        'image',
    ];

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
