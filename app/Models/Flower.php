<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flower extends Model
{
    protected $fillable = ['name', 'variety', 'category', 'unit', 'low_stock_threshold', 'image_url'];

    public function batches(): HasMany
    {
        return $this->hasMany(FlowerBatch::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
