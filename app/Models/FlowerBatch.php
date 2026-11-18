<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FlowerBatch extends Model
{
    protected $fillable = [
        'flower_id',
        'supplier_id',
        'batch_code',
        'quantity',
        'purchase_price',
        'cold_room_location',
        'received_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function flower(): BelongsTo
    {
        return $this->belongsTo(Flower::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function orderItems(): BelongsToMany
    {
        return $this->belongsToMany(OrderItem::class, 'order_item_batches', 'flower_batch_id', 'order_item_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
