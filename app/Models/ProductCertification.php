<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCertification extends Model
{
    protected $fillable = [
        'product_id', 'type', 'number', 'issuer',
        'issued_date', 'image_url', 'document_url', 'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
            'is_verified' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
