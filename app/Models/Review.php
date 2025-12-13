<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
     public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    protected $fillable = [
        'goods_id',
        'user_id',
        'rating',
        'comment',
        'image',
        'status',
    ];

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
    public function scopeVisible($query)
    {
        return $query->where('status', 'approved');
    }
    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function getRatingScoreAttribute(): int
    {
        return (int) $this->votes()->sum('value');
    }
}
