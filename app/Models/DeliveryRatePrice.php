<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryRatePrice extends Model
{
    protected $fillable = [
        'delivery_zone_id',
        'delivery_rate_id',
        'min_weight',
        'max_weight',
        'price',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(DeliveryRate::class, 'delivery_rate_id');
    }

    public function getPriceFormattedAttribute(): string
    {
        return number_format($this->price / 100, 2);
    }
}

