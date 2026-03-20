<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCheckout extends Model
{
    protected $fillable = [
        'cart_id', 'user_id', 'session_id', 'email', 'name', 'phone',
        'cart_total', 'items_count', 'step', 'cart_snapshot', 'recovered',
        'order_id', 'recovered_at', 'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'cart_snapshot' => 'array',
            'recovered' => 'boolean',
            'notified_at' => 'datetime',
            'recovered_at' => 'datetime',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
