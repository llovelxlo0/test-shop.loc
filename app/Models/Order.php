<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    protected $fillable = [
        'user_id',
        'status',
        'recipient_name',
        'phone',
        'address',
        'comment',
        'total',
        'currency',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
