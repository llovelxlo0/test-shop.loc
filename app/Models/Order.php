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
    public const STATUSES = [
        self::STATUS_PENDING   => 'Ожидает оплаты',
        self::STATUS_PAID      => 'Оплачен',
        self::STATUS_SHIPPED   => 'Отправлен',
        self::STATUS_COMPLETED => 'Завершён',
        self::STATUS_CANCELLED => 'Отменён',
    ];
    public const STATUS_COLORS = [
    'pending'   => 'secondary',
    'paid'      => 'primary',
    'shipped'   => 'info',
    'completed' => 'success',
    'cancelled' => 'danger',
];
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

}
