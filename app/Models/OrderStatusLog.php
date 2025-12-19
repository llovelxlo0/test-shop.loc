<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'old_status',
        'new_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
