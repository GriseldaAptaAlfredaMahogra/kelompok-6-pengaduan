<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StoreOrder extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'phone',
        'delivery_address',
        'occasion',
        'requested_delivery_at',
        'status',
        'customization_notes',
        'message_card',
        'subtotal',
        'delivery_fee',
        'total',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_delivery_at' => 'datetime',
            'completed_at' => 'datetime',
            'stock_released_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(OrderReview::class, 'order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending' => 'Menunggu',
            'arranging' => 'Dirangkai',
            'ready' => 'Siap Dikirim',
            'dispatched' => 'Dalam Pengiriman',
            'delivered' => 'Selesai & Terkirim',
            'cancelled' => 'Dibatalkan',
        ][$this->status] ?? ucfirst($this->status);
    }
}
