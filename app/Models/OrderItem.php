<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'bouquet_id',
        'flower_id',
        'item_name',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected function casts(): array
    {
        return ['unit_price' => 'decimal:2', 'line_total' => 'decimal:2'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(StoreOrder::class, 'order_id');
    }

    public function bouquet(): BelongsTo
    {
        return $this->belongsTo(Bouquet::class);
    }

    public function flower(): BelongsTo
    {
        return $this->belongsTo(Flower::class);
    }

    public function batches(): BelongsToMany
    {
        return $this->belongsToMany(FlowerBatch::class, 'order_item_batches', 'order_item_id', 'flower_batch_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
