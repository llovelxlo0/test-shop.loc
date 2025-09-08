<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CategoryType;
use App\Models\CategoryName;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goods extends Model
{
    protected $fillable = ['name', 'description', 'price', 'image'];

    public function categoryType(): BelongsTo {
        return $this->belongsTo(CategoryType::class);
        }

    public function categoryName(): BelongsTo{
        return $this->belongsTo(CategoryName::class);
        }

    /*public function getFullNameAttribute(){
        return trim(($this->categoryType()->name ?? '') . ' ' . ($this->categoryName()->name ?? ''));
        } */
}
